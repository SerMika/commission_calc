# Commission Calculation App

This console application serves the functionality of 
calculating the commissions of some banking operations,
such as "deposit" or "withdraw", which may be 
present in various banking apps.

Application presents one console command which receives 
filepath of a csv file with list of operations as an argument
and displays each operation's commission as a result.

# How to use

First you may want to check `.env` file, where you can check
or change some configurations, such as commission percents
for deposits or withdraw operations.

Next you can run `docker compose up -d` command to start
docker container. You can then enter this container by
`docker exec -it commission_calc_app bash`.

To actually run the command you should use 
`bin/console commission:calculate <filepath>` where 
`<filepath>` should be changed to the csv file path 
relative to the root of this lib. For example, you can use
provided `test.csv` file by running
`bin/console commission:calculate test.csv`

# How to test

To run the tests and code-style check you can use 
`composer run test` command inside docker container.
