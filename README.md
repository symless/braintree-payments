# Braintree Payment Processor

[![Build Status](https://travis-ci.com/symless/braintree-payments.svg?token=qA4emxcJZXUc5WtoVgdL&branch=master)](https://travis-ci.com/symless/braintree-payments)

## Introduction

This package is for processing PayPal payments through Braintree. It is based off the original Laravel Cashier for Braintree beta package, with modifications to allow for one-time payments without the requirements of setting up a billing agreement. This also allows for customers to purchase using their PayPal balance or PayPal Credit (region specific).

## Testing

You will need to set the following details locally and on your Braintree account in order to run the library's tests.

### Local

#### .env

    BRAINTREE_MERCHANT_ID=
    BRAINTREE_PUBLIC_KEY=
    BRAINTREE_PRIVATE_KEY=
    BRAINTREE_MODEL=User

## Deployment

As we are using a private repository for this package, you will need to provide a valid GitHub API token, with sufficient scope, for composer to use to pull the repository.
