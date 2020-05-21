<?php
/**
 * @link http://tt.tryine.com/
 * @copyright Copyright (c) 2018 CSHOP
 * @author Lu Wei
 *
 * Created by Adon.
 * User: Adon
 * Date: 2018/4/26
 * Time: 14:44
 */


namespace app\modules\api\models\mch;

use app\models\Mch;
use app\models\MchCommonCat;
use app\modules\api\models\mch\ShopDataForm;
use app\modules\api\models\ApiModel;
use yii\data\Pagination;
use app\modules\api\models\LocationForm;
use app\models\District;
use app\models\AttentionStore;

class ShopListForm extends ApiModel
{
    public $store_id;
    public $keyword;
    public $cat_id;
    public $page;

    public function rules()
    {
        return [
            [['keyword',], 'trim'],
            [['cat_id', 'page'], 'integer'],
            [['page'], 'default', 'value' => 1,],
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $query = Mch::find()->alias('m')
            ->leftJoin(['mc' => MchCommonCat::tableName()], 'm.mch_common_cat_id=mc.id')
            ->where([
                'm.store_id' => $this->store_id,
                'm.is_delete' => 0,
                'm.is_open' => 1,
                'm.is_lock' => 0,
            ])->orderBy('m.sort,m.addtime DESC');
        if ($this->cat_id) {
            $query->andWhere(['mc.id' => $this->cat_id]);
        }
        if ($this->keyword) {
            $query->andWhere([
                'OR',
                ['LIKE', 'm.name', $this->keyword,],
                ['LIKE', 'mc.name', $this->keyword,],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->page - 1, 'pageSize' => 10,]);
        $list = $query->limit($pagination->limit)->offset($pagination->offset)
            ->select('m.id,m.name,m.logo,m.longitude,m.latitude')
            ->asArray()->all();
        $location=new LocationForm();
        $lng=\Yii::$app->request->get('longitude');
        $lat=\Yii::$app->request->get('latitude');
        $from=[$lng,$lat];
//        var_dump($from);exit;
        $sort=array();
        foreach ($list as $i => &$item) {
            $shop_data_form = new ShopDataForm();
            $shop_data_form->mch_id = $item['id'];
            $shop_data_form->tab = 1;
            $shop_data_form->limit = 3;
            $shop_data = $shop_data_form->search();
            if ($shop_data['code'] != 0) {
                unset($list[$i]);
                continue;
            }
//            var_dump($shop_data);exit;
            $item['data'] = $shop_data['data']['shop'];
            $attention=AttentionStore::findOne(['shop_id' =>$item['id'],'user_id'=>\Yii::$app->user->id]);
            if(is_null($attention)){
                $item['attention']=0;
            }else{
                $item['attention']=1;
            }
            if(!empty($from)){
                $to=[$item['longitude'],$item['latitude']];
                if(empty($item['longitude']) || empty($item['latitude'])){
                    $item['distance']="未知";
                }else{
                    $item['distance']=$location->get_distance($from,$to);
                }

            }else{
                $item['distance']="未知";
            }
            $ids=array();
            $ids=[$item["province_id"],$item["city_id"],$item["district_id"]];
            $loca_info=District::find()->where(["in","id",$ids])->asArray()->all();
            if($loca_info){
                $address="";
                foreach ($loca_info as $k=>$v){
                    $address.=$v['name'];
                }
                $item['address']=$address.$item['address'];
            }
            $sort[$i]=$item['distance'];
        }
//        var_dump($list);exit;
        array_multisort($sort,SORT_ASC,$list,SORT_ASC);
        $cat_list = MchCommonCat::find()
            ->where(['store_id' => $this->store_id, 'is_delete' => 0,])->orderBy('sort ASC')
            ->select('id,name')->asArray()->all();
        return [
            'code' => 0,
            'data' => [
                //'pagination' => $list,
                'list' => $list,
                'cat_list' => $cat_list,
            ],
        ];
    }
}
