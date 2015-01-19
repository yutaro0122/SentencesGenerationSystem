<?php
App::uses('AppModel', 'Model');

class PcvData extends AppModel {
    /**
    *
    * @param string $verb verb
    * @return array probability
    */
    public function getProb($verb, $verb_group) {
        if ($verb === '') {
            return $this->find('all',
                array(
                    'conditions' => array(
                        'PcvData.verb_group' => $verb_group,
                        ),
                    )
                );
        }
        return $this->find('all',
            array(
                'conditions' => array(
                    'PcvData.verb_group' => $verb_group,
                    'PcvData.verb' => $verb,
                    ),
                )
            );
    }
    public function getVerbGroup()
    {
        return $this->find('all',
            array(
                'fields'=>array('DISTINCT verb_group'), // 重複なしの動詞グループリストを返す
                )
            );
    }
    public function getVerbs()
    {
        return $this->find('all',
            array(
                'fields'=>array('verb_group', 'verb'),
                )
            );
    }
}
