<?php

namespace Cerad\Component\DependencyInjection;

/* ==============================================
 * Simple DIC
 * Partially inspired by Pimple
 * Supports tagged services
 */
class Container
{
  private $callables = [];
  private $instances = [];
  private $tags      = [];
  
  // Add a service with optional tagging
  public function set($id,$item,$tagArg = null)
  {
    if (!is_callable($item)) $this->instances[$id] = $item;
    else
    {
      $this->callables[$id] = $item;
      unset($this->instances[$id]);
    }
    if (!$tagArg) return $this;
    
    $tag = is_array($tagArg) ? $tagArg : ['name' => $tag];
    
    $tag['service_id'] = $id;
    
    $name = $tag['name'];
    
    $this->tags[$name][] = $tag;
    
    return $this;
  }
  // Singletons
  public function get($id)
  {
    if (isset($this->instances[$id])) return $this->instances[$id];
    if (isset($this->callables[$id]))
    {
      $item = call_user_func($this->callables[$id],$this);
      return $this->instances[$id] = $item;
    }
    throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined for get.', $id));
  }
  // New instances
  public function create($id)
  {
    if (isset($this->callables[$id]))
    {
      return call_user_func($this->callables[$id],$this);
    }
    throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined for create.', $id));
  }
  public function has($id)
  {
    if (isset($this->instances[$id])) return true;
    if (isset($this->callables[$id])) return true;
    return false;
  }
  // List of services for a given tag
  public function getTags($name = null)
  {
    if (!$name) return $this->tags;
    
    return isset($this->tags[$name]) ? $this->tags[$name] : [];
  }
}