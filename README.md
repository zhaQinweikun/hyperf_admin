<?php

namespace app\controller;

use app\BaseController;
use Elasticsearch\ClientBuilder;
use think\App;
use app\Request;
use think\facade\Db;

class Test extends BaseController
{
    //https://www.jianshu.com/p/afbf3a155d55
    protected $esClent;
    protected $goodsName = "goods_name";
    public function __construct(App $app)
    {
        parent::__construct($app);
        $params = [
            "http://192.168.31.69:9200"
        ];
        $this->esClent =ClientBuilder::create()->setHosts($params)->build();
       if (!$this->exists()){
             $this->createMappings();
//             halt(1);
       }
//       halt(10);
//        try {
////           dump($this->getMappings());
//            $this->deleteIndex();
//        }catch (\Exception $e){
//           dump($e->getMessage()) ;
//           dump($e->getCode()) ;
//        }
    }
    public function index()
    {
        echo time();
    }

    /**
     * 创建数据库
     * @return array
     */
    public function createMappings() {
        $params = [
            "index" => $this->goodsName,
            "body" => [
                "mappings" => [
                    "properties" => [
                        "id" => [
                            "type" => "integer", // 字符串
                        ],
                        "goods_sn" => [
                            "type" => "text"
                        ],
                        "goods_name1" => [
                            "type" => "text",
                            "analyzer" => "ik_max_word",
                        ],
                        "goods_number" => [
                            "type" => "integer"
                        ],
                        "is_hot" => [
                            "type" => "integer"
                        ],
                        "is_on_sale" => [
                            "type" => "integer"
                        ],
                        "is_delete" => [
                            "type" => "integer"
                        ]
                    ],

                ],

            ]
        ];
        return $this->esClent->indices()->create($params);
    }

    /**
     * 获取字段值
     * @return array
     */
    public function getMappings () {
        $params = [
            "index" => $this->goodsName,
        ];
//        return $this->esClent->indices()->exists($params);
        return $this->esClent->indices()->getMapping($params);
    }

    /**
     * 表是否存在
     * @return bool
     */
    public function exists()
    {
        $params = [
            "index" => $this->goodsName,
        ];
        return $this->esClent->indices()->exists($params);
    }

    /**
     * 删除
     * @return array
     */
    public function deleteIndex() {
        $params = ["index" => $this->goodsName];
        $response = $this->esClent->indices()->delete($params);
        return $response;
    }

    /**
     * 插入数据
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function insertData()
    {
          $data =   Db::name('goods')->select();
          foreach ($data as $key => $value){
             $param = [
                 "index" => $this->goodsName,
                 'id' => $value['goods_id'],
                 'body' => [
                    'id' => $value['goods_id'],
                    'goods_sn' => $value['goods_sn'],
                    'goods_name1' => $value['goods_name'],
                    'goods_number' => $value['goods_number'],
                    'is_hot' => $value['is_hot'],
                    'is_on_sale' => $value['is_on_sale'],
                    'is_delete' => $value['is_delete'],
                 ]
             ];
             $this->esClent->index($param);
          }
    }

    /**
     * 获取数据  并且关键词显示高亮
     * @return void
     */
    public function getData()
    {
        $this->update();
        return;
        $param = [
            "index" => $this->goodsName,
            "body" => [
                "query" => [
                    "match" => [
                        "goods_name1" => "玻尿"
                    ]
                ],
                "highlight" => [
                    "pre_tags" => "<b class='key' style='color:red'>",
                    "post_tags"=> "</b>",
                    "fields" => [
                        "goods_name1" => (object)[]
                    ]
                ],
                'from' => 1,
                'size' => 4
            ]
        ];
       $data =  $this->esClent->search($param);
       halt($data);
    }

    /**
     * 修改数据
     * @return void
     */
    public  function update()
    {
        $param = [
            "index" => $this->goodsName,
            "id" =>  1083,
            "body" => [
                'doc' => [
                    "is_hot" => 1
                ],
            ]
       ];
      $res =  $this->esClent->update($param);
      halt($res);
    }


}
