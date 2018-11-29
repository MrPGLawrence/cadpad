(function($, Models, Collections, Views) {

    $(document).ready(function() {

        Views.mangopayPayout = Backbone.View.extend({
            el:'.payout-mangopay-container',
            events: {
                'click .btn-mangopay-payout': 'openBankEditMangopayPayoutModal'
            },
            // initialize : function(options){
            //     this.blockUi = new Views.BlockUi();
            // },
            openBankEditMangopayPayoutModal: function(event){
                event.preventDefault();
                if( typeof MangopayEscrowPayoutForm === 'undefined' ){
                    MangopayEscrowPayoutForm = new Views.MangopayEscrowPayoutForm();

                }
                MangopayEscrowPayoutForm.openModal();
            }
        });
        Views.MangopayEscrowPayoutForm = Views.Modal_Box.extend({
            el: '#mangopay_escrow_modal_payout',
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
                        if(res.Status==="CREATED") {
                            view.closeModal();
                            location.reload();
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Payout Created',
                                notice_type	: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification',{
                                msg	: 'Blocked due to the Bank Account User\'s KYC limitations (maximum debited or credited amount reached)',
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
        new Views.mangopayPayout();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);