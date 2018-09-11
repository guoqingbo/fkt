<?php
/* 将xml字符串转换为数组格式
 * @aryTag 强制生成为数组的标签名 modify:chiwm 200903191642
 * 说明：由于转换之时，记录条数为一的情况，则其不作为数组处理，给解析带来不便，因此加了aryTag参数
 * 举例<Record><x>a</x><y>b</y></Record>和<Record><x>a</x><y>b</y></Record><Record><x>a</x><y>b</y></Record>
 * 在不加参数aryTag="Record"前生成数组为array(1){("Record"=>array(2){"x"=>"a","y=>b"})}
 * 与拥有两条以上条数解析结果array(1){("Record"=>array(2){[0]=>array(2){"x"=>"a","y=>b"}})}解析结果不一致……
 */
function & XML_unserialize(&$xml, $aryTag = "")
{
  $xml_parser = &new XML();
  if ($aryTag != "")
    $xml_parser->setAryTag($aryTag);
  $data = &$xml_parser->parse($xml);
  $xml_parser->destruct();
  return $data;
}

function & XML_serialize(&$data, $level = 0, $prior_key = NULL)
{
  if ($level == 0) {
    ob_start();
    echo '<?xml version="1.0" ?>', "\n";
  }
  while (list($key, $value) = each($data))
    if (!strpos($key, ' attr')) #if it's not an attribute
      #we don't treat attributes by themselves, so for an emptyempty element
      # that has attributes you still need to set the element to NULL

      if (is_array($value) and array_key_exists(0, $value)) {
        XML_serialize($value, $level, $key);
      } else {
        $tag = $prior_key ? $prior_key : $key;
        echo str_repeat("\t", $level), '<', $tag;
        if (array_key_exists("$key attr", $data)) { #if there's an attribute for this element
          while (list($attr_name, $attr_value) = each($data["$key attr"]))
            echo ' ', $attr_name, '="', htmlspecialchars($attr_value), '"';
          reset($data["$key attr"]);
        }

        if (is_null($value)) echo " />\n";
        elseif (!is_array($value)) echo '>', htmlspecialchars($value), "</$tag>\n";
        else echo ">\n", XML_serialize($value, $level + 1), str_repeat("\t", $level), "</$tag>\n";
      }
  reset($data);
  if ($level == 0) {
    $str = &ob_get_contents();
    ob_end_clean();
    return $str;
  }
}

###################################################################################
# XML class: utility class to be used with PHP's XML handling functions
###################################################################################
class XML
{
  var $parser;   #a reference to the XML parser
  var $document; #the entire XML structure built up so far
  var $parent;   #a pointer to the current parent - the parent will be an array
  var $stack;    #a stack of the most recent parent at each nesting level
  var $last_opened_tag; #keeps track of the last tag opened.
  var $aryTag;
  var $blnFirstAryTag = true;

  function XML()
  {
    $this->parser = &xml_parser_create();
    xml_parser_set_option(&$this->parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_object(&$this->parser, &$this);
    xml_set_element_handler(&$this->parser, 'open', 'close');
    xml_set_character_data_handler(&$this->parser, 'data');
  }

  function destruct()
  {
    xml_parser_free(&$this->parser);
  }

  function & parse(&$data)
  {
    $this->document = array();
    $this->stack = array();
    $this->parent = &$this->document;

    $result = xml_parse(&$this->parser, &$data, true) ? $this->document : NULL;

    return $result;
  }

  function open(&$parser, $tag, $attributes)
  {
    $this->data = ''; #stores temporary cdata
    $this->last_opened_tag = $tag;
    if (is_array($this->parent) and array_key_exists($tag, $this->parent)) { #if you've seen this tag before
      if (is_array($this->parent[$tag]) and array_key_exists(0, $this->parent[$tag])) { #if the keys are numeric
        #this is the third or later instance of $tag we've come across
        $key = count_numeric_items($this->parent[$tag]);
      } else {
        #this is the second instance of $tag that we've seen. shift around
        if (array_key_exists("$tag attr", $this->parent)) {
          $arr = array('0 attr' => &$this->parent["$tag attr"], &$this->parent[$tag]);
          unset($this->parent["$tag attr"]);
        } else {
          $arr = array(&$this->parent[$tag]);
        }
        $this->parent[$tag] = &$arr;
        $key = 1;
      }
      $this->parent = &$this->parent[$tag];
    } else {
      $key = $tag;
    }
    if ($attributes) $this->parent["$key attr"] = $attributes;
    if ($this->blnFirstAryTag && $tag == $this->aryTag) {
      $this->parent = &$this->parent[$key][];
      $this->blnFirstAryTag = false;
    } else
      $this->parent = &$this->parent[$key];
    $this->stack[] = &$this->parent;
  }

  function data(&$parser, $data)
  {
    if ($this->last_opened_tag != NULL) #you don't need to store whitespace in between tags
      $this->data .= $data;
  }

  function close(&$parser, $tag)
  {
    if ($this->last_opened_tag == $tag) {
      $this->parent = $this->data;
      $this->last_opened_tag = NULL;
    }
    array_pop($this->stack);
    if ($this->stack) $this->parent = &$this->stack[count($this->stack) - 1];
  }

  //设置强制数组的标签名
  function setAryTag($tag)
  {
    $this->aryTag = $tag;
  }
}

function count_numeric_items(&$array)
{
  return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0;
}

?>
