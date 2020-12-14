# README

### 1.0.0
	
* 单元测试`70%`

### 1.1.0
	
* 升级:  "chloroplast/marmot-dev-pacakges": "1.0.1"

### 1.1.1

* `RowQueryFindable.class.php`
	* 添加`join`方法
* `MyPdo`, 添加字符集配置信息, `database.charset`
* `JsonApiTrait`
	* `formatParameters`->`protected`

```
return [

//database
'database.host'     => 'mysql',
'database.port'     => 3306,
'database.dbname'   => 'credit_data',
'database.user'     => 'root',
'database.password' => '123456',
'database.tablepre' => 'pcore_',
'database.charset'  => 'utf8mb4'
];
```

* `IRowQuery`
	* `find`
	* `count`
	* `join`
