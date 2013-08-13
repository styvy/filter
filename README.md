filter
======

Data Filtering Class

= Usage
$filter = Filter::create($_POST)
			->all(
				array(
					'trim',
					array('trim', array('#value', '/ ')),
					array('trim', '#value'),
				)
			)
			->add(
				array('name'),
				array(
					//array('strip_tags'),
					array('filter_var', array('#value', FILTER_SANITIZE_STRING))
				)
			)
			->add('age', array('intval'))
			->add('age2', array('floatval'))
			->add(
				'street',
				array(
					'App_Filter::qqq',
					array('str_replace', array('Sch', '---', '#value')),
					//array('ltrim', array(':val', '-'))
				)
			)
			
			->filter()
		;
		
		//dd($filter['name']);
		dd($filter->getData());
