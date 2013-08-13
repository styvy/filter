# Filter


Data Filtering Class

#### Usage

Initialize:

+ Use all() method to add the filter(s) for all fields
+ Use add() method to add the filter(s) for one or more fields

<pre><code>$filter = Filter::create($_POST)
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
		)
;

</pre></code>

And execute:
<pre>
$filter->filter();
</pre>


#### Get Data

Data array:
<pre>
var_dump($filter->getData());
</pre>

or ArrayAccess interface:
<pre>
echo $filter['firstname'];
echo $filter['address'];
</pre>
