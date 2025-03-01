<?php
namespace Pephpit\HttpClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Pephpit\HttpClient\Client;
use Pephpit\HttpClient\ClientCollection as TestedClass;
use mageekguy\atoum;

class ClientCollection extends atoum\test {

	public function getCollection(){
		$ClientCollection = new TestedClass();

		$ClientCollection->add(new Client('http://jsonplaceholder.typicode.com/posts'));
		$ClientCollection->set('comments', new Client('http://jsonplaceholder.typicode.com/comments'));
		$ClientCollection['users'] = new Client('http://jsonplaceholder.typicode.com/users');

		return $ClientCollection;
	}

	public function testGet(){

		$ClientCollection = $this->getCollection();

		$this->object($ClientCollection[0])->isInstanceOf('\\Pephpit\HttpClient\\Client');
		$this->object($ClientCollection['comments'])->isInstanceOf('\\Pephpit\HttpClient\\Client');
		$this->object($ClientCollection->get('users'))->isInstanceOf('\\Pephpit\HttpClient\\Client');
		$this->variable($ClientCollection['unknow'])->isNull();

	}

	public function testCount(){
		$ClientCollection = $this->getCollection();

		$this->integer(count($ClientCollection))->isEqualTo(3);
	}

	public function testNav(){
		$ClientCollection = $this->getCollection();

		$this->object($ClientCollection->first())->isInstanceOf($ClientCollection[0]);
		$this->object($ClientCollection->next())->isIdenticalTo($ClientCollection['comments']);
		$this->object($ClientCollection->current())->isIdenticalTo($ClientCollection['comments']);
		$this->object($ClientCollection->last())->isIdenticalTo($ClientCollection['users']);
		$this->string($ClientCollection->key())->isEqualTo('users');
	}

	public function testExist(){
		$ClientCollection = $this->getCollection();
		
		$this->boolean($ClientCollection->exists(0))->isTrue();
		$ClientCollection->remove(0);
		$this->boolean($ClientCollection->exists(0))->isFalse();

		$this->boolean(isset($ClientCollection['comments']))->isTrue();
		unset($ClientCollection['comments']);
		$this->boolean(isset($ClientCollection['comments']))->isFalse();

	}

	public function testGetIterator(){
		$ClientCollection = $this->getCollection();

		foreach ($ClientCollection as $key => $Client){
			$this->object($Client)->isInstanceOf('\\Pephpit\HttpClient\\Client');
		}

	}

	public function testRequest(){
		$ClientCollection = $this->getCollection();

		$ResponseCollection = $ClientCollection->execute();

		$this->integer(count($ResponseCollection))->isEqualTo(3);
		$this->object($ResponseCollection)->isInstanceOf('\\Pephpit\HttpClient\\ResponseCollection');
		$this->object($ResponseCollection[0])->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->object($ResponseCollection['comments'])->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->object($ResponseCollection['users'])->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->boolean($ResponseCollection[0]->isSuccessful())->isTrue();
		$this->boolean($ResponseCollection['comments']->isSuccessful())->isTrue();
		$this->boolean($ResponseCollection['users']->isSuccessful())->isTrue();

	}

	public function testExceptions() {
		$ClientCollection = new TestedClass();

		$this->exception(
			function() use($ClientCollection) {
				$ClientCollection->add(new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Pephpit\HttpClient\Client');

		$this->exception(
			function() use($ClientCollection) {
				$ClientCollection->set('stdclass', new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Pephpit\HttpClient\Client');

		$this->exception(
			function() use($ClientCollection) {
			$ClientCollection['stdclass'] = new \stdClass();
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$client must be an instance of \Pephpit\HttpClient\Client');

	}


}
