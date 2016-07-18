<?php

$people = '{"data":[{"first_name":"matt","last_name":"stauffer","age":31,"email":"matt@stauffer.com","secret":"VXNlIHRoaXMgc2VjcmV0IHBocmFzZSBzb21ld2hlcmUgaW4geW91ciBjb2RlJ3MgY29tbWVudHM="},{"first_name":"dan","last_name":"sheetz","age":99,"email": "dan@sheetz.com","secret":"YWxidXF1ZXJxdWUuIHNub3JrZWwu"},]}';

/**
 * Instructions:
 *
 * Given the above JSON, build a simple PHP script to import it.
 *
 * Your script should create two variables:
 *
 * - a comma-separated list of email addresses
 * - the original data, sorted by age descending, with a new field on each record
 *   called "name" which is the first and last name joined.
 * 
 * Please deliver your code in either a GitHub Gist or some other sort of web-hosted code snippet platform.
 */



class PeopleDataTransformer
{
  /**
   * List of people
   */
  private $list;

  /**
   * Construct a new transformer
   *
   * @param String $json list of people
   */
  public function __construct($json)
  {
    $this->list = json_decode(
      $this->removeTrailingCommas($json)
    );

    // bail on error
    if (is_null($this->list))
    {
      throw new Exception(error_get_last()['message']);
    }
  }

  /**
   * Returns all email addresses found as a csv
   * 
   * @return String
   */
  public function getEmailAddressesAsCsv()
  {
    return implode(',', array_map(function($person) {
      return $person->email;
    }, $this->list->data));
  }

  /**
   * Returns all the original data with an extra
   * computed name property
   */
  public function addComputedNameProperty()
  {
    foreach ($this->list->data as $person)
    {
      $person->name = $person->first_name . ' ' . $person->last_name;
    }
  }

  /**
   * Sorts the people in the list by their age
   */
  public function orderBy($key, $order = 'ASC')
  {
    usort($this->list->data, function($a, $b) use ($key, $order)
    {
      $reverse = strtolower($order) == 'desc' ? -1 : 1;

      if ($a->$key === $b->$key)
      { 
        return 0;
      }

      if (is_string($a->$key))
      {
        return strcmp($a->$key, $b->$key) * $reverse;
      }

      return $a->$key < $b->$key
        ? -1 * $reverse
        :  1 * $reverse;
    });
  }

  /**
   * Returns the list of people
   * 
   * @return array
   */
  public function people()
  {
    return $this->list->data;
  }

  /**
   * Source below taken from 
   * http://php.net/manual/en/function.json-decode.php
   * to remove trailing commas which is not valid 
   * JSON according to spec http://es5.github.io/#x11.1.5
   * 
   * @param  [type] $json [description]
   * @return [type]       [description]
   */
  private function removeTrailingCommas($json)
  {
      $json = preg_replace('/,\s*([\]}])/m', '$1', $json);

      return $json;
  }
}


// create a transformer
$transformer = new PeopleDataTransformer($people);


// print out the email addresses
print "<pre>Emails: " . $transformer->getEmailAddressesAsCsv() . PHP_EOL;


// print out the data with the name property
$transformer->addComputedNameProperty();
$transformer->orderBy('age', 'DESC');
print PHP_EOL . "Transformed people list" . PHP_EOL;
var_dump( $transformer->people() );
print PHP_EOL . "Data as json" . PHP_EOL;
print json_encode($transformer->people());
print "</pre>";

?>
