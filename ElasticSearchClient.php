<?php

/**
 * Class ElasticSearchClient
 *
 * @package app\common\components
 */
final class ElasticSearchClient {
    /**
     * @var string
     */
    protected $url;

    /**
     * ElasticSearchClient constructor.
     *
     * @param string      $url
     * @param null|string $index
     */
    public function __construct($url = 'http://localhost:9200') {
        $this->url = $url;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $data
     *
     * @return mixed
     * @throws Exception
     */
    protected function call($method = 'GET', $path = NULL, $data = [], $index = '') {
        $url = $this->url . '/';
        if ($index) {
            $url .= $index . '/';
        }
        if ($path) {
            $url .= $path;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $return = json_decode($response);
        unset($response);
        return $return;
    }

    /**
     * 创建索引
     */
    public function create($index) {
        $this->call('PUT', NULL, NULL, $index);
    }

    /**
     * 删除索引
     */
    public function drop($index) {
        $this->call('DELETE', NULL, NULL, $index);
    }

    /**
     * 获取全部别名
     *
     * @return mixed
     */
    public function aliases() {
        return $this->call('GET', '_aliases', NULL);
    }

    public function indices() {
        $result = $this->stats();
        if (!$result) {
            return NULL;
        }
        return $result;
    }


    /**
     * 获取索引状态
     *
     * @return mixed
     */
    public function stats($index = NULL) {
        return $this->call('GET', '_stats', NULL, $index);
    }

    /**
     * 获取索引状态
     *
     * @return mixed
     */
    public function status($index) {
        return $this->call('GET', '_status', NULL, $index);
    }

    /**
     * curl -X GET http://localhost:9200/{INDEX}/{TYPE}/_count -d {matchAll:{}}
     *
     * @param $type
     *
     * @return mixed
     */
    public function count($index, $type) {
        return $this->call('GET', $type . '/_count', '{ matchAll:{} }', $index);
    }

    /**
     * curl -X PUT http://localhost:9200/{INDEX}/{TYPE}/_mapping -d ...
     *
     * @param string $index
     * @param string $type
     * @param array  $data
     *
     * @return mixed
     */
    public function map($index, $type, $data) {
        return $this->call('PUT', $type . '/_mapping', $data, $index);
    }

    /**
     * 往索引中添加内容
     *
     * @param string $index
     * @param string $type
     * @param int    $id
     * @param array  $data
     *
     * @return mixed
     */
    public function add($index, $type, $id, $data) {
        return $this->call('PUT', $type . '/' . $id, $data, $index);
    }

    /**
     * curl -X GET http://localhost:9200/{INDEX}/{TYPE}/_search?q= ...
     *
     * @param string $index
     * @param string $type
     * @param string $q
     *
     * @return mixed
     */
    public function query($index, $type, $q) {
        return $this->call('GET', $type . '/_search?' . http_build_query(['q' => $q]), NULL, $index);
    }
}