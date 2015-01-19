<?php
$this->assign('title_for_layout', 'Sentence Generation System');
echo $this->Form->create(false, array('type'=>'post', 'action'=>'/search'));
echo $this->Form->input('searched_verb', array('label'=>'動詞を入力してください', 'default'=>'', 'type'=>'text'));
echo $this->Form->end('検索');
?>
<table>
    <?php
    if ($error) {
        echo '<tr><td>' . $error . '</td></tr>';
    } elseif ($results) {
        echo '
        <tr>
            <th>動詞グループ</th>
            <th>動詞</th>
        </tr>
        ';
	foreach($results as $key_num=>$f_value){ // added
		foreach ($f_value as $key => $value) {
        	    echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
        	}
	} // added
    } else {
        echo '<tr><td>検索したい動詞を入力してください</td></tr>';
    }
    ?>
</table>
