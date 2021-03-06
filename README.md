# PHP typecast

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6f827635-029d-44bb-9482-2487b7d4a39a/big.png)](https://insight.sensiolabs.com/projects/6f827635-029d-44bb-9482-2487b7d4a39a)

[![Build Status](https://travis-ci.org/alexpts/php-typecast.svg?branch=master)](https://travis-ci.org/alexpts/php-typecast)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-typecast/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-typecast/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-typecast/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-typecast)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-typecast/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-typecast/?branch=master)

Cast types

#### Install
`composer require alexpts/php-typecast`

#### Example

```php
$body = [
    'title' => 'Some',
    'user' => [
        'name' => 'Alex'
        'age' => '29',
        'isAdmin' = 'false',
    ],
    'friendsIds' => ['1', '2', '3', 4],
    'date' => '11-12-2017',
    'date2' => '11-12-2017',
];

$caster = new TypeCast(new DeepArray);

// shot format
$data = $caster->cast($body, [
    'friendsIds' => ['array', ['each' => ['int']]],
    'title' => ['string'],
    'user' => ['array'],
    'user.name' => ['string'],
    'user.age' => ['int'],
    'user.isAdmin' => ['bool'],
    'date' => ['datetime'],
    'date2' => ['datetime', ['datetimeFormat' => ['Y-m-d']]], // full format
]);

/*
$data ==== [
    'title' => 'Some',
    'user' => [
        'name' => 'Alex'
        'age' => 29,
        'isAdmin' = false,
    ],
    'friendsIds' => [1, 2, 3, 4],
    'date' => new \DateTime('11-12-2017'),
    'date2' => '2017-12-11',
];
*/
```


#### Types:

##### string
Set type to string

##### bool
Set type to boolean

##### int
Set type to int

##### array
Set type to array

##### float
Set type to float

##### object
Set type to float

##### null
Set type to null

##### numbers
Remove all not number symbols from string

##### each
Convert each array item to describe types (see example above)

##### datetime
Convert value to \DateTime object with default timezone

##### datetimeFormat
Convert \DateTime object to string via format method

#### Custom convert type
You can add custom convert via method on TypeCast service

```php
public function registerType(string $name, callable $handler): self
```
