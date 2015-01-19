<?php
App::uses('Component', 'Controller');

/**
 * 確率値関連の共通処理
 */
class NgramComponent extends Component {
      public function ngram($str, $num){
        $ret = array();

        // 1文字ずつに分割した配列を作成
        $matches = array();
        if (!empty($str)) {
            $matches = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        }
        $_len = count($matches); // 文字列の長さを取得

        if ($num > 0 && $_len > $num) {
            // N = 1 の場合は、分割した配列をそのまま代入
            if ($num == 1) {
                $ret = $matches;
            } else {
                // 配列のポジションを制御する配列を作成
                $_pos= array_pad(array(), $num, 0);

                // メインルーチン
                for($_pos[0]=0; $_pos[$num-1]<$_len-1; $_pos[0]++) {
                    for ($i=1; $i<$num; $i++) {
                        $_pos[$i] = $_pos[$i-1] + 1;
                    }

                    $_tmp = '';
                    for ($i=0; $i<$num; $i++) {
                        $_tmp .= $matches[$_pos[$i]];
                    }
                    $ret[] = $_tmp;
                }
            }
        } else {
            // 対象文字列が N 以下の場合は、分割せずにそのまま出力
            $ret[] = $str;
        }

        // MySQLの全文検索用文字列を作る場合は半角スペース区切りで出力
        return $ret;
    }
}