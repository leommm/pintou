<?php
/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/11/22
 * Time: 18:08
 */

namespace app\modules\mch\models\group;

use app\models\SpCat;
use app\modules\mch\models\MchModel;
use yii\data\Pagination;

class SpCatForm extends MchModel
{
    public $cat;

    public $name;
    public $store_id;
    public $mch_id;
    public $pic_url;
    public $sort;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'store_id', 'pic_url'], 'required'],
            [['store_id', 'sort', 'mch_id'], 'integer','min'=>0],
            [['pic_url'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['sort'],'default','value'=>0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标题名称',
            'store_id' => '商城ID',
            'pic_url' => '分类图片url',
            'sort' => '排序 升序',
            'addtime' => 'Addtime',
            'is_delete' => 'Is Delete',
			'mch_id' => '商户id'
        ];
    }

    /**
     * 获取分类列表
     */
    public function getList($store_id, $mch_id = 0)
    {
        $query = SpCat::find()
            ->andWhere(['is_delete'=>0,'store_id'=>$store_id,'mch_id'=>$mch_id]);
        $count = $query->count();
        $p = new Pagination(['totalCount' => $count, 'pageSize' => 20]);
        $list = $query->orderBy('sort ASC')
            ->offset($p->offset)
            ->limit($p->limit)
            ->all();
        return [$list, $p];
    }

    /**
     * @return array
     * 拼团分类编辑
     */
    public function save()
    {
        if ($this->validate()) {
            $cat = $this->cat;
            if ($cat->isNewRecord) {
                $cat->is_delete = 0;
                $cat->addtime = time();
            }
            $cat->attributes = $this->attributes;
            if ($cat->save()) {
                return [
                    'code' => 0,
                    'msg' => '保存成功',
                ];
            } else {
                return $this->getErrorResponse($cat);
            }
        } else {
            return $this->errorResponse;
        }
    }
}
