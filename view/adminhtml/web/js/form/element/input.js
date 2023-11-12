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
            store: ko.observable(null),
            enabled: ko.observable(false),
            ruleId: null,
            imports: {
                checkEligibilityForCustomer: '${ $.parentName }.customer_id:value',
                setIsEnabled: '${ $.provider }:data.rule_eligibility_checker_enabled',
                storeToCheckEligibility: '${ $.provider }:data.store_to_check_customer_eligibility'
            }
        },

        /**
         * Initializes the component.
         *
         * @return {this} Returns the instance of this component.
         */
        initialize: function () {
            this._super();
            this.disableIfApplicable();

            let segments = window.location.pathname.split('/');
            let idIndex = segments.indexOf('id');

            if (idIndex !== -1 && segments.length > idIndex + 1) {
                this.ruleId = segments[idIndex + 1];
            }

            return this;
        },

        /**
         * Set if the component must be shown based on module's config and if it's a new rule.
         *
         * @param {boolean} enabled
         */
        setIsEnabled: function (enabled) {
            let isNewRule = window.location.pathname.split('/').indexOf('new') !== -1;
            this.enabled(!isNewRule && enabled);
        },

        /**
         * Hide the component if it's a new rule (not saved yet).
         *
         */
        disableIfApplicable: function () {
            this.visibleContainer(this.enabled());
        },

        /**
         * Sets the store ID to get the eligibility of the customer.
         *
         * @param {string} storeId - The store ID.
         */
        storeToCheckEligibility: function (storeId) {
            this.store(storeId);
        },

        /**
         * Hides/shows the container of the module in the cart rule form.
         *
         * @param {boolean} visible
         */
        visibleContainer: function (visible) {
            uiRegistry.async('index = rule_eligibility_container')(function (container) {
                container._elems.forEach(function (element) {
                    uiRegistry.async(element)(function (elementUiClass) {
                        elementUiClass.visible(visible);
                    });
                });
            });
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
         * Call the eligibility checker service to check if the rule is applicable
         * for the given customer and update the component accordingly.
         *
         * @return {void}
         */
        checkEligibilityForCustomer: function () {
            let ajaxUrl = urlBuilder.build(this.getEligibilityUrl);
            let customerId = this.value();

            $.ajax({
                showLoader: true,
                url: ajaxUrl,
                data: {
                    rule_id: this.ruleId,
                    store_id: this.store(),
                    customer_id: customerId,
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
