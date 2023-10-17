// noinspection JSUnresolvedVariable

/**
 * Rule Eligibility Check - Magento 2 UI Component
 *
 * @module Ytec_RuleEligibilityCheck/js/form/element/abstract
 */
define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'mage/url',
    'mage/translate',
    'ko'
], function ($, uiRegistry, Abstract, urlBuilder, urlFetcher, __, ko) {
    'use strict';

    /**
     * @class
     * @extends Magento_Ui/js/form/element/abstract
     */
    return Abstract.extend({
        /**
         * Default properties of the object.
         */
        defaults: {
            customScope: 'data',
            messageText: ko.observable(''),
            success: ko.observable(false),
            ruleId: null,
            imports: {
                update: '${ $.parentName }.customer_id:value',
                isEnabled: '${ $.provider }:data.rule_eligibility_checker_enabled',
            }
        },

        /**
         * Initializes the component.
         *
         * @return {this} Returns the instance of this component.
         */
        initialize: function () {
            this._super();

            let segments = window.location.pathname.split('/');
            let idIndex = segments.indexOf('id');

            if (idIndex !== -1 && segments.length > idIndex + 1) {
                this.ruleId = segments[idIndex + 1];
            }

            return this;
        },

        /**
         * Hide/Show the component based on module's config.
         *
         * @param {boolean} enabled
         */
        isEnabled: function (enabled) {
            this.visible(enabled);
        },

        /**
         * Sets the message to be displayed.
         *
         * @param {string} newMessage - The message to be displayed.
         */
        setMessage: function(newMessage) {
            this.messageText(`<strong>${newMessage}</strong>` + (this.success() ? ' 😊' : ' 😔'));
        },

        /**
         * Sets the success flag of the operation.
         *
         * @param {boolean} isSuccess - True if successful, false otherwise.
         */
        setSuccess: function(isSuccess) {
            this.success(isSuccess);
        },

        /**
         * Updates the component based on the value of the associated UI form element.
         *
         * @param {string|number} value - The value of the associated form element.
         */
        update: function (value) {
            let ajaxUrl = urlBuilder.build(this.getEligibilityUrl);
            let customerId = this.value();

            $.ajax({
                showLoader: true,
                url: ajaxUrl,
                data: {
                    rule_id: this.ruleId,
                    customer_id: customerId
                },
                type: "POST",
                dataType: 'json'
            }).done(function (response) {
                this.setSuccess(response.isCustomerEligible);
                this.setMessage(response.message);
            }.bind(this)).fail(function () {
                this.setSuccess(false);
                this.setMessage(__('Error: Something went wrong!'));
            }.bind(this));
        }
    });
});
