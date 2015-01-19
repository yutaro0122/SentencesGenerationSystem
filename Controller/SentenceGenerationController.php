<?php
App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

/**
 * Show and input any words.
 */
class SentenceGenerationController extends AppController {

    public $uses = array('PcvData', 'Pcn1Data', 'Pn2cData');
    public $components = array('Probability', 'RequestHandler', 'Ngram');
    public $helpers = array('Js'=>array('Jquery'));
    const NUM_OF_FIELDS = 5;

    /**
     *
     */
    public function index() {
        $this->layout = 'SentenceGeneration';
        $results = array();
        $db = array(
            'subj' => 'Pcn1Data',
            'obj' => 'Pn2cData',
            'verb' => 'PcvData'
            );
        $verb_group_options = $this->PcvData->getVerbGroup(); // [num]['PcvData'][verb_group]
        $e = null;
        $num = 1;
        $errors = array();
        if ($this->request->data) {
            $words = array(
                'subj' => mb_convert_kana($this->request->data['subject'], 'a'),
                'obj' => mb_convert_kana($this->request->data['object'], 'a'),
                'verb_group' => $this->request->data['verb_group'],
                'verb' => $this->request->data['verb'],
                );
            try {
                $no_input = 0;
                if ($words['subj'] === '') {
                    $no_input++;
                }
                if ($words['obj'] === '') {
                    $no_input++;
                }
                if ($words['verb'] == '') {
                    $no_input++;
                }
                if ($no_input > 1) {
                    $e = new Exception('主語（名詞）、目的語（名詞）、述語（動詞）の内、2ヶ所以上入力してください', 1, $e);
                }
                if ($words['verb_group'] == '') {
                    $e = new Exception('動詞グループは必ず選択してください', 1, $e);
                }
                if ($this->request->data['num']) {
                    $num = mb_convert_kana($this->request->data['num'], 'a');
                } else {
                    $e = new Exception('出力数を入力してください', 1, $e);
                }
                if ($e) {
                    throw $e;
                }
                $word_and_probs = array(
                    'subj' => $this->Pcn1Data->getProb($words['subj'], $words['verb_group']),
                    'obj' => $this->Pn2cData->getProb($words['obj'], $words['verb_group']),
                    'verb' => $this->PcvData->getProb($words['verb'], $words['verb_group']),
                    );

                $num_of_results = 0;
                for ($num_subj=0; $num_subj < count($word_and_probs['subj']); $num_subj++) {
                    for ($num_obj=0; $num_obj < count($word_and_probs['obj']); $num_obj++) {
                        for ($num_verb=0; $num_verb < count($word_and_probs['verb']); $num_verb++) {
                            $results[$num_of_results] = $this->Probability->calcProb(
                                    $word_and_probs['subj'][$num_subj][$db['subj']],
                                    $word_and_probs['obj'][$num_obj][$db['obj']],
                                    $word_and_probs['verb'][$num_verb][$db['verb']]
                                    );
                            $num_of_results++;
                        }
                    }
                }

                switch ($num_of_results) {

                    // if any array has no element (if not found any words in database)
                    case 0:
                        $not_found_array = array_fill(0, self::NUM_OF_FIELDS, '-');
                        $position = 0;
                        foreach (array_keys($word_and_probs) as $key) {
                            if (!count($word_and_probs[$key])) {
                                $not_found_array[$position] = 'データベース中に見つかりませんでした';
                            }
                            // for passing the verb group column
                            $position = $position == 1 ? $position+2 : $position+1;
                        }
                        $results[0] = $not_found_array;
                        $num = 1;
                        break;

                    // if subj, obj and verb all were input
                    case 1:
		    	$results[0]['sum'] = number_format($results[0]['sum'], 4);
                        $num = 1;
                        break;

                    default:
                        foreach ($results as $key => $row) {
                            $prob_key[$key] = $row['sum'];
                        }
                        array_multisort($prob_key, SORT_DESC, $results);
                        foreach (array_keys($results) as $key) {
                            if (is_double($results[$key]['sum'])) {
                                $results[$key]['sum'] = number_format($results[$key]['sum'], 4);
                            }
                        }
                        if ($num > $num_of_results) {
                            $num = $num_of_results;
                        }
                        break;
                }

            } catch (Exception $e) {
                do {
                    $errors[] = $e->getMessage();
                } while ($e = $e->getPrevious());
            }
        }

        $vgo = array();
        foreach (array_keys($verb_group_options) as $key) {
            $word = $verb_group_options[$key][$db['verb']]['verb_group'];
            $vgo[$word] = $word;
        }
        $this->set('errors', array_reverse($errors));
        $this->set(compact('vgo'));
        $this->set('num', Sanitize::clean($num));
        $this->set('results', Sanitize::clean($results));
    }

    public function ajax_verb_options() {
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect(array('controller'=>'sentence_generation', 'action'=>'index'));
        }
        $vo = array(''=>"");
        $verb_options = $this->PcvData->getVerbs(); // [num]['PcvData'][verb_group/verb]
        foreach (array_keys($verb_options) as $key) {
            if ($verb_options[$key]['PcvData']['verb_group'] == $this->request->query['data']['verb_group']) {
                $word = $verb_options[$key]['PcvData']['verb'];
                $vo[$word] = $word;
           }
        }
        $this->set(compact('vo'));
    }

    public function search() {
        $this->layout = 'SentenceGeneration';
        $verbs = $this->PcvData->getVerbs();
        $results = array();
        $error = '';
        if ($this->request->data) {
            $searched_verb = $this->request->data['searched_verb'];
            try{
                if ($searched_verb === '') {
                    throw new Exception('動詞を入力してください', 1);
                }
                foreach ($verbs as $key => $value) {
		    $splited_verb = $this->Ngram->ngram($value['PcvData']['verb'], mb_strlen($searched_verb));
		    foreach($splited_verb as $num_verb => $character_verb) { // added
		    	if ($character_verb == $searched_verb) { // added
			     array_push($results, array($value['PcvData']['verb_group']=>$value['PcvData']['verb'])); // added
			} // added
		    }
                }
                if (empty($results)) {
                    $error = 'データベース中に見つかりませんでした';
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->set('error', Sanitize::clean($error));
        $this->set('results', Sanitize::clean($results));
    }

}
