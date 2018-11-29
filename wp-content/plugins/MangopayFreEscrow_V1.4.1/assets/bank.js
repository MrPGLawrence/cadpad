(function($, Models, Collections, Views) {

    $(document).ready(function() {

        Views.mangopayBankUpdate = Backbone.View.extend({
            el:'.update-mangopay-container',
            events: {
                'click .btn-update-mangopay-bank': 'openBankEditMangopayModal'
            },
            // initialize : function(options){
            //     this.blockUi = new Views.BlockUi();
            // },
            openBankEditMangopayModal: function(event){
                event.preventDefault();
                if( typeof MangopayEscrowBankForm === 'undefined' ){
                    MangopayEscrowBankForm = new Views.MangopayEscrowBankForm();

                }
                MangopayEscrowBankForm.openModal();
            }
        });
        $('.formhide').hide()

        $('#cases').change(function() {
            var value = this.value;
            $('.formhide').hide()
            $('#' + this.value).show();
        });
        Views.MangopayEscrowBankForm = Views.Modal_Box.extend({
            el: '#mangopay_escrow_modal_bank',
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
                        if(res===null) {
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Incorrect Details',
                                notice_type	: 'error'
                            });
                        }
                        if(res.Id) {
                            view.closeModal();
                            location.reload();
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Success',
                                notice_type	: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Invalid bank account data',
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
        new Views.mangopayBankUpdate();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);