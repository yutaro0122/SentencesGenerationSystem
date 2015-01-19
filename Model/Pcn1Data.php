<?php
App::uses('AppModel', 'Model');

class Pcn1Data extends AppModel {
    /**
    *
    * @param string $subject subject_noun
    * @return array probability
    */
    public function getProb($subject, $verb_group) {
        if ($subject === '') {
            return $this->find('all',
                array(
                    'conditions' => array(
                        'Pcn1Data.verb_group' => $verb_group,
                        ),
                    )
                );
        }
        return $this->find('all',
            array(
                'conditions' => array(
                    'Pcn1Data.verb_group' => $verb_group,
                    'Pcn1Data.subject' => $subject,
                    ),
                )
            );
    }
}
