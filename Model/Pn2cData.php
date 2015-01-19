<?php
App::uses('AppModel', 'Model');

class Pn2cData extends AppModel {
    /**
    *
    * @param string $object object_noun
    * @return array probability
    */
    public function getProb($object, $verb_group) {
        if($object === '') {
            return $this->find('all',
                array(
                    'conditions' => array(
                        'Pn2cData.verb_group' => $verb_group,
                        ),
                    )
                );
        }
        return $this->find('all',
            array(
                'conditions' => array(
                    'Pn2cData.verb_group' => $verb_group,
                    'Pn2cData.object' => $object,
                    ),
                )
            );
    }

}
