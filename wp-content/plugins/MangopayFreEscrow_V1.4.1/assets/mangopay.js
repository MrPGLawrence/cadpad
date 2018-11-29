// // (function($) {
// //     $(document).ready(function() {
// //         $(".btn-submit").click(function(){
// //
// //
// //             bday = $('#dob').val();
// //             var obj = {};
// //             obj['first_name'] = $('#first_name').val();
// //             obj['last_name'] = $('#last_name').val();
// //             obj['user_email'] = $('#user_email').val();
// //             obj['bday'] = bday;
// //             obj['nationality'] = $('#nationality').val();
// //             obj['country'] = $('#country').val();
// //             obj['action']= 'fre-mangopay-escrow-customer';
// //             $.ajax({
// //                 type : 'post',
// //                 url: ae_globals.ajaxURL,
// //                 data : obj,
// //
// //                 beforeSend : function () {
// //                 },
// //                 success : function (res) {
// //                     view.blockUi.unblock();
// //                     if(res.success) {
// //                         AE.pubsub.trigger('ae:notification',{
// //                             msg	: res.msg,
// //                             notice_type	: 'success'
// //                         });
// //                     } else {
// //                         AE.pubsub.trigger('ae:notification',{
// //                             msg	: res.msg,
// //                             notice_type	: 'error'
// //                         });
// //                     }
// //                 }
// //             })
// //         });
// //     });
// // })( jQuery);
//
//
 (function($, Models, Collections, Views) {
    $(document).ready(function() {
        Views.mangopayUpdate = Backbone.View.extend({
            el:'.update-mangopay-container',
            events: {
                'click .btn-update-mangopay': 'openEditMangopayModal'
            },
            // initialize : function(options){
            //     this.blockUi = new Views.BlockUi();
            // },
            openEditMangopayModal: function(event){
                event.preventDefault();
                if( typeof MangopayEscrowForm === 'undefined' ){
                    MangopayEscrowForm = new Views.MangopayEscrowForm();
                }
                MangopayEscrowForm.openModal();
            }
        });
        Views.MangopayEscrowForm = Views.Modal_Box.extend({
            el: '#mangopay_escrow_modal',
            events: {
                'submit form#mangopay_form': 'submitMangopay'
            },
            initialize: function(options) {
                Views.Modal_Box.prototype.initialize.apply(this, arguments);
                // bind event to modal
                _.bindAll(this, 'mangopayResponseHandler');
                this.blockUi = new Views.BlockUi();
            },
            submitMangopay: function(event) {
                event.preventDefault();
                var $form = $(event.currentTarget);
                this.blockUi.block($form);
                var view = this;
                var data = $form.serialize();
                $.ajax ({
                    type : 'post',
                    url  : ae_globals.ajaxURL,
                    data : data,
                    beforeSend : function () {
                    },
                    success : function (res) {
                        view.blockUi.unblock();
                        if(res.Id) {
                            view.closeModal();
                            location.reload();
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Success',
                                notice_type	: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'error',
                                notice_type	: 'error'
                            });
                        }
                    }
                });
                // event.createToken($form, this.mangopayResponseHandler);
            },
            mangopayResponseHandler: function(status, response) {
                var view = this;
                if (status !== 200 && response.error !== undefined) {

                    view.blockUi.unblock();
                    return false;
                } else {
                    view.saveRipientInfo(response);
                }
            },
            saveRipientInfo : function (res) {

            }
        });
        new Views.mangopayUpdate();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);