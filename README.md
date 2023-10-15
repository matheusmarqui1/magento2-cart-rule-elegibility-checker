# Ytec Rule Eligibility Check for Magento 2

## Introduction

Welcome to Ytec Rule Eligibility Check for Magento 2. This module is a masterpiece coming straight from the magical city of Porto, meticulously engineered by Ytec, a company of Matheus da Costa Marqui. It simplifies rule eligibility checks to an unprecedented level. 😎

## Features

- **Date Validation**: Validate whether the rule's date range is applicable.
- **Item Actions Validation**: Validate cart items against rule actions.
- **Rule Conditions Validation**: Validate rule conditions against the entire cart.
- **Shipping Address Validation**: Validate shipping addresses against rule conditions.

## Prerequisites

- PHP 7.4 or higher
- Magento 2.4.x

## Installation

1. Run `composer require ytec/rule-eligibility-check` in your Magento root directory.
2. Execute `bin/magento setup:upgrade`.
3. Clear the cache by running `bin/magento cache:clean` and `bin/magento cache:flush`.

## How to Use

### Finding the Module in Admin

1. Login to your Magento Admin Panel.
2. Navigate through `Marketing > Promotions > Cart Price Rules`.
3. Once you're inside, look for a new field labeled `Customer Id to test rule eligibility`.

### Using the Module

1. **Input Customer ID**: Enter the customer ID you want to test for eligibility in the aforementioned field.
2. **Press Validate**: A button labeled "Validate" should appear next to the input field. Click it.
3. **Check Result**: Upon pressing validate, you'll either see a success or an error message.
    - Success Message: "Oohah. The customer can benefit of this rule!"
    - Error Message: "Oops. The customer is not eligible for this rule!"

## Dependencies

This module depends on the following Magento 2 modules:

- `Magento_SalesRule`
- `Magento_Quote`

## License

This module is open-source but all credits belong to Ytec, a company of Matheus da Costa Marqui. For the full license, please refer to the LICENSE.md file.

## Support and Contribution

For bugs, issues or feature requests, please open an issue on the repository or send an email to matheus.701@live.com for more personalized assistance.

---

Copyright (c) 2023 Ytec, a company of Matheus da Costa Marqui (matheus.701@live.com)
