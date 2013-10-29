<?php

class FormField{
	private $name;
	private $value;
	private $type;
	private $id;
	private $class_attribute;
	private $attributes;
	private $check_type;
	private $check_arg;
	private $label;
	
	function FormField($name = NULL, $value = NULL, $type = "text", $label = NULL ,$class = NULL, $check_type = NULL, $check_arg = NULL, $attributes = NULL){
		$this->name = $name;
		$this->value = $value;
		$this->type = $type;
		$this->class_attribute = $class;
		$this->check_type = $check_type;
		$this->check_arg = $check_arg;
		$this->attributes = $attributes;
		$this->label = $label;
	}
	
	function getName(){
		return $this->name;
	}
	
	function getValue(){
		return $this->value;
	}
	
	function getType(){
		return $this->type;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getClassAttribute(){
		return $this->class_attribute;
	}
	
	function getLabel(){
		return $this->label;
	}
	
	function getAttributes(){
		return $this->attributes;
	}
	
	function setName($name){
		$this->name = $name;
	}
	
	function setValue($value){
		$this->value = $value;
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	function setId($id){
		$this->id = $id;
	}
	
	function setLabel($label){
		$this->label = $label;
	}
	
	function getClass($class){
		$this->class_attribute = $class;
	}
}

//Definisce un tag form HTML
class Form{
	static $n;
	private $fields;
	private $action;
	private $method;
	private $class_attribute;
	private $id;
	
	function Form($action, $method = "get", $class = NULL, $id = NULL){
		$this->action = $action;
		if($method == "get" || $method == "post")
			$this->method = $method;
		$this->class_attributes = $class;
		if(!($id === NULL)){
			$this->id = "form_" + $n;
		}
	}
	 
	//Aggiunge un campo al form.
	function addField($field){
		is_a($field, "FormField") or die("Il valore che hai inserito nel Form non &egrave; di tipo FormField.");
		$field->setId($this->id."_".$field->getName());
		if(!isset($this->fields)){
			$this->fields = array($field);
		}
		else
			$this->fields[] = $field;
	}
	
	//Stampa il form
	function printForm(){?>
    	<form	action="<?php echo $this->action ?>" 
        		method="<?php echo $this->method ?>" 
				<?php echo $this->class_attribute === NULL ? "" : ("class="+$this->class) ?>>
		<?php
		foreach($this->fields as $f){
			if(!($f->getLabel() === NULL))
				echo '<label for="',$f->getId(),'">',$f->getLabel(),'</label>';
			echo '<input type="',$f->getType(),'" name="',$f->getName(),'" value="',$f->getValue(),'" class="',$f->getClassAttribute(),'" id="',$f->getId(),'" ',$f->getAttributes(), ' >';
        }
		?>
        </form>
    	<?php
	}
	
	function getFields(){
		return $this->fields;
	}
	
	function getAction(){
		return $this->action;
	}
	
	function getMethod(){
		return $this->method;
	}
	
	function getClassAttribute(){
		return $this->class_attribute;
	}
	
	function getId(){
		return $this->id;
	}
	
	function setFields($field_array){
		foreach($field_array as $f) is_a($f, "FormField") or die("I valori inseriti non sono di tipo FormField");
		$this->fields = $field_array;
	}
	
	function setAction($action){
		is_string($action) or die("Il valore inserito non e' una stringa");
		$this->action = $action;
	}
	
	function setMethod($method){
		if($method == "get" || $method == "post")
			$this->method = $method;
		else
			die ("Il valore inserito deve essere \"get\" o \"post\"");
	}
	
	function setClass($class){
		is_string($class) or die("Il valore inserito non e' una stringa");
		$this->class = $class;
	}
	
	function setId($id){
		is_string($id) or die("Il valore inserito non e' una stringa");
		$this->id = $id;
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento senza titolo</title>
</head>

<body>
<?php

$form = new Form("prova.php", "get");
$nome = new FormField("name", "", "text", "Nome");
$cognome = new FormField("surname", "", "text", "Cognome");
$form->addField($nome);
$form->addField($cognome);
$form->printForm();

?>
</body>
</html>