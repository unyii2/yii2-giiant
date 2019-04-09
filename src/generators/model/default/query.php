<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{


    /**
    * @param string $fieldName
    * @param string $dateRange
    * @return self
    */
    public function andFilterWhereDateRange(string $fieldName, $dateRange): self
    {
        if(empty($dateRange)){
            return $this;
        }

        $list = explode(' - ', $dateRange);
        if(count($list) !== 2){
            return $this;
        }

        return $this->andFilterWhere(['between', $fieldName, $list[0], $list[1]]);
    }
}
