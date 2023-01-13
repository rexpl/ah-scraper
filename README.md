# Albert Heijn scraper
Small scraper to collect product info from Albert Heijn categories.

- [Installation](#installation)
- [Usage](#usage)

## Installation

### From git:

First run:
```
git clone https://github.com/rexpl/ah-scraper
```

Once the repository cloned:
```
cd ./ah-scraper
composer install
```

### If from the zip file:

Unzip the file and in the project root run:
```
composer install
```

## Usage

In the project root run:
```
php scraper
```
To see a list of all options run:
```
php scraper -h
```
The appropriate command for this exercise would be:
```
php scraper bier-en-aperitieven.csv -n
```
