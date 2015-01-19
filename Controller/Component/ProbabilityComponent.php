<?php
App::uses('Component', 'Controller');

/**
 * 確率値関連の共通処理
 */
class ProbabilityComponent extends Component {

    /**
     * sum of $prob_subject * $prob_object * $prob_verb
     *
     * @param array $prob_subject
     * @param array $prob_object
     * @param array $prob_verb
     * @return double $prob
     */
    public function calcProb($prob_subject, $prob_object, $prob_verb) {
        $sum = 0;

        $prob_types = array(
            $prob_subject,
            $prob_object,
            $prob_verb,
            );
        $class_ids = array('c0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9');

        // take $prob_subject, $prob_object, $prob_verb out from $prob_types
        for ($i=0; $i < 10; $i++) {
            $product = 1;
            for ($j=0; $j < 3; $j++) {
                $product *= $prob_types[$j][$class_ids[$i]];
            }
            $sum += $product;
        }
        return array(
            'subj' => $prob_subject['subject'],
            'obj' => $prob_object['object'],
            'verb' => $prob_verb['verb'],
            'verb_group' => $prob_verb['verb_group'],
            'sum' => $sum,
            );
    }

}
