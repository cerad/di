<?php

namespace Cerad\Component\DependencyInjection;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
  protected $testClassName = '\Cerad\Component\DependencyInjection\ContainerTestClass';
  
  public function test1()
  {
    $container = new Container();

    $container->set('scalar',42);
    $this->assertEquals(42, $container->get('scalar'));
    
    $this->assertTrue ($container->has('scalar'));
    $this->assertFalse($container->has('scalaR'));
  }
  /**
   * @expectedException \InvalidArgumentException
   */
  public function testNotDefinedException()
  {
    $container = new Container();
    $container->get('xxx');
  }
  public function test2()
  {
    $container = new Container();
    
    $func1 = function()
    {
      return 42;
    };
    $container->set('func1',$func1);
    $this->assertEquals(42, $container->get('func1'));
  }
  public function test3()
  {
    $container = new Container();
    $container->set('i42',42);
    
    $func1 = function(Container $c)
    {
      return $c->get('i42');
    };
    $container->set('func1',$func1);
    $this->assertEquals(42, $container->get('func1'));
  }
  public function testClass()
  {
    $container = new Container();
    
    $container->set('i42',42);
    
    $func = function(Container $c)
    {
      $item = new $this->testClassName($c->get('i42'));
      return $item;
    };
    $container->set('func',$func);
    $this->assertEquals(42, $container->get('func')->get());
  }
  public function testClassUse()
  {
    $container = new Container();
    
    $container->set('i42',42);
    
    $i = 21;
    
    $func = function(Container $c) use($i)
    {
      $item = new $this->testClassName($c->get('i42'));
      /** @noinspection PhpUndefinedMethodInspection */
      $item->set($i);
      return $item;
    };
    $container->set('func',$func);
    $this->assertEquals(21, $container->get('func')->get());
  }
  public function testTags()
  {
    $container = new Container();
    $container->set('container_tags',[]);
    
    $func = function()
    {
      $item = new $this->testClassName(42);
      return $item;
    };
    $container->set('func',$func,['name' => 'function', 'param' => 'p42']);
/*
    $functions = $container->getTags('function');
    
    $this->assertEquals(1,count($functions));
    
    $tag = $functions[0];
    $this->assertEquals('p42',$tag['param']);*/
  }
}
class ContainerTestClass
{
  private $i;
  
  public function __construct($i)
  {
    $this->i = $i;
  }
  public function get() { return $this->i; }
  public function set($i) { $this->i = $i; }
}