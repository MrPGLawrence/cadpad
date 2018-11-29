<?php 
 if( get_option('es_offer_halloween_done_2018_email_subscribers') == 'no' ) return;
?>
<style type="text/css">
.es_offer{
    width: 70%;
    height: auto;
    margin: 1em auto;
    text-align: center;
    background-color: #f18322;
    font-size: 1.2em;
    letter-spacing: 3px;
    line-height: 1em;
    padding: 1.2em;
    background-image: url('<?php echo  ES_URL ?>/images/happy-hallowen.png');
    background-repeat: no-repeat;
    background-size: 40%;
    background-position: left;
}
.es_offer_heading{
    color: #FFF;
    padding: 1em 0;
    line-height: 1em;
    margin-left: 9em;
}
.es_main_heading {
    font-size: 5em;
    color: #b70f0f;
    font-weight: 600;
    line-height: 1em;
    position: relative;
}

.es_text{
    font-size: 0.9em;
}
.es_left_text{
    padding: 0.5em 0em;
    color: #000;
}
.es_right_text{
    color: #FFFFFF;
    font-weight: 600;
    max-width: 50%;
    padding: 10px 56px;
    width: auto;
    margin: 0.3em 0em;
    display: inline-block;
    text-decoration: none;
    background: #b70f0f;
    font-size: 2em;
    line-height: 1.2em;
}
.es_right_text:hover, .es_right_text:active{
    color: inherit; 
}
.es_offer_content{
    margin-left: 35%;
}
</style>
<div class="es_offer">
    <div style="float:right;"><img src="<?php echo  ES_URL ?>/images/icegram-logo-16bit-gray-30.png"  /></div>
    <div  class="es_offer_content">
        <div class="es_offer_heading">&mdash; Halloween Sale &mdash;</div>
        <div class="es_main_heading">Upto 50% OFF</div>
        <div class="es_text">
            <div class="es_left_text" style="font-size:1.1em;">Offer applicable on</div>
            <div class="es_left_text" style="font-size:1.1em;"> <span style="color:#FFF;font-weight:bold">Icegram, Rainmaker & Email Subscribers</span></div>
            <a href="?dismiss_admin_notice=1&option_name=es_offer_halloween_done_2018" target="_blank" class="es_right_text">Claim This Offer</a>
            <div class="es_left_text">Offer ends on 2nd November, 2018 - so hurry.. </div>
        </div>
    </div>
</div>