if (!Detector.webgl) {
    Detector.addGetWebGLMessage();
}

var containerGroup = new Array();
var cameraGroup = new Array();
var controlsGroup = new Array();
var sceneGroup = new Array();
var rendererGroup = new Array();
var modelLinks = new Array();

var windowRatio = 1.5;
var windowWidth = window.innerWidth/windowRatio;
var windowHeight = window.innerHeight1/windowRatio;

jQuery(document).ready(function() {
    // Get model links and init.
    modelLinks = jQuery('.list-file-attack .file-attack-name a');
    BuildModelViewers(modelLinks);
    animate();
});

// Update model viewers when a new model is succesfully uplaoded.
jQuery(document).ajaxSuccess(function() {
    var newModelLinks = jQuery('.list-file-attack .file-attack-name a');
    if(modelLinks.length != newModelLinks)
    {
        newModelLinks.each(function (i)
        {
            var newModelLink = jQuery(this).attr('href');
            var newModelLinkText = jQuery(this).text();
            var newModelA = this;
            for(var ii = 0; ii < modelLinks.length; ii++)
            {
                var modelLinkText = jQuery(modelLinks[ii]).text();
                if(newModelLinkText == modelLinkText)
                {
                    break;
                }
                if(ii == modelLinks.length-1)
                {
                    BuildModelViewer(newModelLink, newModelA);
                }
            }
        });
        modelLinks = newModelLinks;
    }

    // Add download links on ajax success(not required as page reloads on job completion)
    //
    // if(jQuery('.attach-file').length == 0)
    // {
    //     for(var i = 0; i < modelLinks.length; i++)
    //     {
    //         var target = jQuery(modelLinks[i]);
    //         if(!target.parent().find('.dlm-btn'))
    //         {
    //             jQuery('<a class="dlm-btn fre-btn" href="' + target.attr('href') +'">Download</a>').insertAfter(target);
    //         }
    //     }
        
    // }
});

function BuildModelViewers(DOMElements)
{
    jQuery(DOMElements).each(function ()
    {
        BuildModelViewer(jQuery(this).attr('href'), this);
    });
}

function BuildModelViewer(file, link)
{
    windowWidth = window.innerWidth/windowRatio;
    windowHeight = window.innerHeight/windowRatio;
    var fileExtension = file.substring(file.length-3).toLowerCase().trim();
    if(fileExtension !== "obj" && fileExtension !== "stl") return;
    var container, camera, controls, scene, renderer;
    var lighting, ambient, keyLight, fillLight, backLight;

    container = document.createElement('div');
    document.body.appendChild(container);

    camera = new THREE.PerspectiveCamera(45, windowWidth / windowHeight, 1, 1000);
    camera.position.z = 1;
    
    scene = new THREE.Scene();
    
    ambient = new THREE.AmbientLight(0xffffff, 0.8);
    
    keyLight = new THREE.DirectionalLight(new THREE.Color('hsl(30, 100%, 75%)'), 0.8);
    keyLight.position.set(-100, 0, 100);
    
    fillLight = new THREE.DirectionalLight(new THREE.Color('hsl(240, 100%, 75%)'), 0.5);
    fillLight.position.set(100, 0, 100);
    
    backLight = new THREE.DirectionalLight(0xffffff, 0.8);
    backLight.position.set(100, 0, -100).normalize();
    
    scene.add(ambient);
    scene.add(keyLight);
    scene.add(fillLight);
    scene.add(backLight);
    
    //TODO: Move to seperate function.
    switch(fileExtension)
    {
        case "obj" :
            var loader = new THREE.OBJLoader();
            loader.load(file, function (group) {
                var geometry = group.children[0].geometry;
                var material = new THREE.MeshLambertMaterial();
                material.color = new THREE.Color("#33cc33");
                material.emissiveIntensity = 3;
                
                var mesh = new THREE.Mesh( geometry, material );                
                var box = new THREE.Box3().setFromObject( mesh );
                box.getCenter(mesh.position);
                mesh.position.multiplyScalar( - 1 );
                var pivot = new THREE.Group();
                scene.add( pivot );
                pivot.add( mesh );

                mesh.geometry.computeBoundingBox()
                var pos = 2 * Math.atan( mesh.geometry.boundingBox.getSize().y / ( 2 * camera.position.z ) ) * ( 180 / Math.PI );
                camera.position.z = pos;
                camera.updateProjectionMatrix();
            });
        break;
        case "stl" :
            var loader = new THREE.STLLoader();
            loader.load(file, function (geometry) {
                var material = new THREE.MeshLambertMaterial();
                material.color = new THREE.Color("#33cc33");
                material.emissiveIntensity = 3;
                
                var mesh = new THREE.Mesh( geometry, material );                
                var box = new THREE.Box3().setFromObject( mesh );
                box.getCenter(mesh.position);
                mesh.position.multiplyScalar( - 1 );
                var pivot = new THREE.Group();
                scene.add( pivot );
                pivot.add( mesh );

                mesh.geometry.computeBoundingBox()
                var pos = 2 * Math.atan( mesh.geometry.boundingBox.getSize().y / ( 2 * camera.position.z ) ) * ( 180 / Math.PI );
                camera.position.z = pos;
                camera.updateProjectionMatrix();
            });
        break;
    }
            
    renderer = new THREE.WebGLRenderer();
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(windowWidth, windowHeight);
    renderer.setClearColor(new THREE.Color("hsl(0, 0%, 10%)"));
    container.appendChild(renderer.domElement);
    
    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.25;
    controls.enableZoom = true;
    controls.enablePan = false;
    
    cameraGroup.push(camera);
    containerGroup.push(container);
    sceneGroup.push(scene);
    rendererGroup.push(renderer);
    controlsGroup.push(controls);

    var viewerID = containerGroup.length-1 + '-model-viewer';
    jQuery(container).attr('id', viewerID);
    jQuery(container).addClass("mfp-hide");
    jQuery(container).addClass("white-popup");
    jQuery(link).magnificPopup({
        items: {
          src: '#' + viewerID,
          type: 'inline'
        }     
    });

    if(jQuery('.attach-file').length == 0 && !jQuery(link).hasClass('preview-model'))
    {
        var templateUrl = object_name.templateUrl;
        jQuery('<a class="dlm-btn fre-btn" href="' + file +'">Download</a>').insertAfter(jQuery(link))
        var tempBtn = jQuery('<button class="prn-btn fre-btn" type="submit" value="Print">Print</button>').insertAfter(jQuery(link))
        tempBtn.click(function(e)
        {
            var buttons = jQuery('.prn-btn');
            e.preventDefault();  
            jQuery(buttons).each(function()
            {
                jQuery(this).attr('disabled', 'disabled');
                jQuery(this).addClass('disabled');
            });
            jQuery.ajax({
                type: 'POST',
                url: templateUrl + '/3dhubs/print.php',
                data: { 
                    'file': file
                },
                success: function(response){
//                    window.location.href = response;                  
			window.open = (response,'_blank');
                },
                complete: function(response)
                {            
                    jQuery(buttons).each(function()
                    {
                        jQuery(this).removeAttr('disabled');
                        jQuery(this).removeClass('disabled');
                    });
                }
            });
        });
    }
    jQuery(link).attr('href', "");
}

window.addEventListener('resize', onWindowResize, false);
function onWindowResize() {
    windowWidth = window.innerWidth/windowRatio;
    windowHeight = window.innerHeight/windowRatio;
    for(var i = 0; i < cameraGroup.length; i++)
    { 
        cameraGroup[i].aspect = windowWidth / windowHeight;
        cameraGroup[i].updateProjectionMatrix();
        rendererGroup[i].setSize(windowWidth, windowHeight);
    }
}

function animate() {
    requestAnimationFrame(animate);
    for(var i = 0; i < controlsGroup.length; i++)
    { 
        controlsGroup[i].update();
    }
    render();
}

function render() {
    for(var i = 0; i < rendererGroup.length; i++)
    { 
        rendererGroup[i].render(sceneGroup[i], cameraGroup[i]);
    }
}
