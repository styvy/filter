Filter
======

Data Filtering Class

**Usage**

<pre><code>
$filter = Filter::create($_POST)
	->all(
		array('trim')
	)
	->add(
		array('firstname', 'lastname),
			array(
				array('filter_var', array('#value', FILTER_SANITIZE_STRING))
			)
		)
	->add('age', array('intval'))
	->add(
		'address',
			array(
				'MyClass::myFilter',
				array('str_replace', array('123123', '', '#value'))
			)
		)->filter()
;
var_dump($filter->getData());
</pre></code>
