Doc Blocker
===========
[![Build Status](https://travis-ci.org/warmans/docblocker.svg?branch=master)](https://travis-ci.org/warmans/docblocker)[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/warmans/docblocker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/warmans/docblocker/?branch=master)[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/warmans/docblocker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/warmans/docblocker/?branch=master)

Analyses doc block coverage and quality across a PHP project.

## Usage

    ./docblocker parse /path/to/project/src

### Arguments

| Argument      | Description                                   | Required          |
| ------------- | --------------------------------------------- | ----------------- |
| target        | The path to your project source               | Yes               |


### Options

| Option        | Description                                   | Status            |
| ------------- | --------------------------------------------- | ----------------- |
| --report-json | Output a JSON report to the given path        | Not implemented   |
| --report-text | Output a plain text report to the given path  | Not implemented   |
| --report-xml  | Output an XML report to the given path        | Not implemented   |