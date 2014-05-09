# Simple PHP Web Crawler

### Getting started

* Create a database table

```
CREATE TABLE swc_url (
  url    VARCHAR(255) NOT NULL,
  dat    INT(10)      NOT NULL,
  status TINYINT(1)   NOT NULL,
  PRIMARY KEY (url)
)
```

* Copy the file `config.php.template` to `config.php` and set the values
* Run the `index.php` 