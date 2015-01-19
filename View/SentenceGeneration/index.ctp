<?php echo $this->Html->script('jquery'); ?>
<!-- Input any words -->
<?php
// 動詞グループ切り替えのJS
$this->Js->get('#verb_group')->event(
    'change',
    $this->Js->request(
        array('controller'=>'sentence_generation','action'=>'ajax_verb_options'),
        array('update' => '#verb', 'dataExpression' => true, 'data' => '$("#verb_group").serialize()')
    )
);
echo $this->Js->writeBuffer();
?>
<?php
$this->assign('title_for_layout', 'Sentence Generation System');
echo $this->Form->create(false, array('type'=>'post', 'action'=>'.'));
echo $this->Form->input('subject', array('label'=>'主語（名詞）', 'default'=>'', 'type'=>'text'));
echo $this->Form->input('object', array('label'=>'目的語（名詞）', 'default'=>'', 'type'=>'text'));
echo $this->Form->input('verb_group', array(
    'label'=>'述語（動詞グループ）',
    'type'=>'select',
    'options'=>$vgo,
    'selected'=>'選択してください',
    'empty'=>'選択してください',
    )
);
echo $this->Form->input('verb', array(
    'label'=>'述語（動詞）',
    'type'=>'select',
    'empty'=>'選択してください',
    )
);
echo $this->Html->link('動詞の検索', array(
    'controller'=>'sentence_generation',
    'action'=>'search',
    ), array(
    'id' => 'search',
    'target' => '_blank',
    )
);
echo $this->Form->input('num', array('label'=>'出力数', 'default'=>30, 'type'=>'text'));
echo $this->Form->end('検索');
?>
<table>
    <?php
    if ($errors) {
        echo '<tr><td><font color="ff0000"><b>エラー:</b></font></td></tr>';
        foreach ($errors as $key => $value) {
            echo '<tr><td><font color="#ff0000">' . $value . '</font></td></tr>';
        }
    } elseif($results) {
        echo '
        <tr>
            <th>順序</th>
            <th>主語（名詞）</th>
            <th>目的語（名詞）</th>
            <th>述語（動詞）</th>
            <th>動詞グループ</th>
            <th>出力数</th>
        </tr>
        ';
        for ($i=0; $i < $num; $i++) {
            echo '<tr>';
            echo '<td>' . ($i+1) . '</td>';
            foreach ($results[$i] as $key => $value) {
                echo '<td>' . $value .'</td>';
            }
            echo "</tr>\n";
        }
    } else {
        echo '
        <tr><td>「名詞(S)が名詞(O)を動詞(V)」という形式の文を生成します</td></tr>
        <tr><td>例: 主語（名詞）と述語（動詞）を入力して、目的語（名詞）を入力しなかったとき、ふさわしい目的語（名詞）のリストを出力します</td></tr>
        <tr><td>主語（名詞）、目的語（名詞）、述語（動詞）の内、2ヶ所以上入力してください</td></tr>
        <tr><td>動詞グループは必ず選択してください</td></tr>
        ';
    }
    ?>
</table>
