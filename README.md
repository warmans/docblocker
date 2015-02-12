Doc Blocker
===========
[![Build Status](https://travis-ci.org/warmans/docblocker.svg?branch=master)] (https://travis-ci.org/warmans/docblocker)[![Scrutinizer Code Quality] (https://scrutinizer-ci.com/g/warmans/docblocker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/warmans/docblocker/?branch=master)[![Code Coverage](https://scrutinizer-ci.com/g/warmans/docblocker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/warmans/docblocker/?branch=master)

Analyses doc block coverage and quality across a PHP project.

## Usage

    ./docblocker parse /path/to/project/src

### Arguments

| Argument      | Description                                   | Required          |
| ------------- | --------------------------------------------- | ----------------- |
| target        | The path to your project source               | Yes               |


### Options

| Option                | Description                                               |
| --------------------- | --------------------------------------------------------- |
| --report-text         | Output a text report to the given path instead of stdout  |
| --report-json         | Output a JSON report to the given path                    |
| --fail-score-below    | Exit non-zero if the project score is below given value   |
| --no-progress         | Omit progress bars from output                            |

