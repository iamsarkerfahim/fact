# Summary
The application takes a simple JSON based DSL as an input. Then performs following actions:

- check the format of the JSON is correct
- the value of the *security* is exists in **dataSource/securities.csv**
- check the expression is in correct format e.g.
    - fn: contains + or - or / or *
    - a and b: is number or another expression or exists in **dataSource/attributes.csv*
- perform the calculation recursively based on the expression

### Sample expressions
<code>
{
  "expression": {"fn": "*", "a": "sales", "b": 2},
  "security": "ABC"
}
  
{
  "expression": {"fn": "/", "a": "price", "b": "eps"},
  "security": "BCD"
}
  
{
  "expression": {
    "fn": "-", 
    "a": {"fn": "-", "a": "eps", "b": "shares"}, 
    "b": {"fn": "-", "a": "assets", "b": "liabilities"}
  },
  "security": "CDE"
}
</code> 

# Tech Scope
- 5.4
- PHP 7.4
- JQuery 3.6
- Bootstrap 5.0

# How to run
- cd into the project root
- run **symfony server:start** (you may need to install symfony cli from https://symfony.com/download)
- go to http://127.0.0.1:39419/ (ip address and port number may chnaged, please the output from the server:start)

# Further Improvement
1) add a firewall to make sure api only accepted from the relevant IP
2) Improve UI to collect the data to build the expression
3) Currently the alert message remain hidden after closing using the close icon
4) Store the csv using something like The Cache Component (https://symfony.com/doc/current/components/cache.html), or redis/memcache, or db instead of loading from the file on every request
5) Add monolog for logging
6) Addid unit test and functional test
7) At the moment the error.log file is generated in the public root is not got practice for security








