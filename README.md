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


### Sample Text Report

```
Processing Files...
 3/3 [============================] 100%  1 sec

Analysing...
 3/3 [============================] 100%  1 sec

Project
--------------------------------------------------------------------------------
Overall Score: 4.72 / 10

Coverage
--------------------------------------------------------------------------------
Class Coverage: 100%
Method Coverage: 60%
Interface Coverage: 0%

Issues
--------------------------------------------------------------------------------
\MyProject\Sub\B::foo scored 0 out of 10
- Add method docblock

\MyProject\Sub\B::bar scored 2.5 out of 10
- Add a method description
- Not enough param tags. Method has 2 arguments but has 1 param tags

\MyProject\Sub\BInterface::foo scored 0 out of 10
- Add method docblock
```