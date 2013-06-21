<?php
	echo form_open('user/group/'.$group_id);
	foreach ($checkbox as $key => $value) {
		echo '<label for="$value->id" class="mycustomclass" style="color: #000;">'.$privileges[$key]->name.'</label>';
		echo form_checkbox($value);
	}
	echo form_submit('update', 'Update');
	echo form_close();
?>