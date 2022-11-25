<?php declare(strict_types = 1);

// spell-check-ignore: ahaha algo binaryblafasel2 binaryrpl blafasel2 joe wp

namespace SqlFtw\Tests\Mysql;

trait Aliases
{

    /** @var array<string, string> */
    private static array $aliases = [
        // scopes
        '@@local.' => '@@session.',
        'set local ' => 'set @@session.',
        'set session ' => 'set @@session.',
        'set global ' => 'set @@global.',
        'set persist ' => 'set @@persist.',
        'set persist_only ' => 'set @@persist_only.',
        'set @@session.' => 'set @@session.',
        'set @@global.' => 'set @@global.',
        'set @@persist.' => 'set @@persist.',
        'set @@persist_only.' => 'set @@persist_only.',
        'reset @@persist.' => 'reset persist ',
        'show local variables' => 'show session variables',
        'set @@global.transaction isolation level' => 'set global transaction isolation level', // fix

        // IN -> FROM
        'show columns in' => 'show columns from',
        'show events in' => 'show events from',
        'show indexes in' => 'show indexes from',
        'show keys in' => 'show indexes from',
        'show open tables in' => 'show open tables from',
        'show table status in' => 'show table status from',
        'show tables in' => 'show tables from',
        'show triggers in' => 'show triggers from',

        // DATABASE -> SCHEMA
        'alter database' => 'alter schema',
        'create database' => 'create schema',
        'drop database' => 'drop schema',
        'show create schema' => 'show create database', // todo: flip?
        'show schemas' => 'show databases', // todo: flip?

        // TABLE -> TABLE[S]
        'lock table ' => 'lock tables ',
        'unlock table ' => 'unlock tables ',
        'flush table ' => 'flush tables ',
        'flush table;' => 'flush tables;',
        'analyze tables ' => 'analyze table ',
        'optimize tables ' => 'optimize table ',
        'rename tables ' => 'rename table ',
        'drop tables ' => 'drop table ',
        'drop temporary tables ' => 'drop temporary table ',
        'check tables ' => 'check table ',
        'show index ' => 'show indexes ',

        // FIELDS -> COLUMNS
        'show fields' => 'show columns',
        'show full fields' => 'show full columns',

        // KEY -> INDEX
        'add key' => 'add index',
        'fulltext key' => 'fulltext index',
        'spatial key' => 'spatial index',
        'unique key ' => 'unique index ',
        'unique key,' => 'unique index,',
        'unique key;' => 'unique index;',
        'unique key(' => 'unique index(',
        'unique key)' => 'unique index)',
        'drop key' => 'drop index',
        'show keys' => 'show indexes',
        'use key for' => 'use index for',
        'force key for' => 'force index for',
        'ignore key' => 'ignore index',
        'force key(' => 'force index(',
        ',key(' => ',index(',
        ', key(' => ', index(',
        'show extended index ' => 'show extended indexes ',

        // MASTER -> BINARY
        'purge master logs' => 'purge binary logs',
        'show master logs' => 'show binary logs',

        // NO_WRITE_TO_BINLOG -> LOCAL
        'analyze no_write_to_binlog' => 'analyze local',
        'flush no_write_to_binlog' => 'flush local',
        'optimize no_write_to_binlog' => 'optimize local',
        'repair no_write_to_binlog' => 'repair local',

        // transactions
        'begin work' => 'start transaction',
        'commit work' => 'commit',
        'rollback work' => 'rollback',

        // [DEFAULT]
        'default character set' => 'character set',
        'default charset' => 'character set',
        'default encryption' => 'encryption',

        // other synonyms
        'describe select' => 'explain select',
        'sql_buffer_result distinct' => 'distinct sql_buffer_result',
        'columns terminated by' => 'fields terminated by',
        'drop prepare' => 'deallocate prepare',
        'distinctrow' => 'distinct',
        'kill connection' => 'kill',
        'kill query' => 'kill',
        'revoke all privileges' => 'revoke all',
        'show engines' => 'show storage engines',
        'xa begin' => 'xa start',
        'type btree' => 'using btree',
        'sqlstate value' => 'sqlstate',
        ' value(' => ' values(',
        ' value (' => ' values (',

        // multiplications
        'sql_buffer_result distinct distinct' => 'distinct sql_buffer_result',
        'distinct sql_buffer_result distinct' => 'distinct sql_buffer_result',
        'sql_small_result sql_small_result sql_small_result' => 'sql_small_result',

        // column options ordering
        'primary key auto_increment not null' => 'not null auto_increment primary key',
        'primary key not null auto_increment' => 'not null auto_increment primary key',
        'primary key not null invisible' => 'not null invisible primary key',
        'primary key auto_increment' => 'auto_increment primary key',
        'primary key invisible' => 'invisible primary key',
        'primary key not null' => 'not null primary key',
        'unique index not null' => 'not null unique',
        'unique key not null' => 'not null unique',
        'unique not null' => 'not null unique',
        'unique null' => 'null unique',
        'unique auto_increment' => 'auto_increment unique',
        'unique invisible' => 'invisible unique',
        'key auto_increment' => 'auto_increment key',
        'auto_increment not null' => 'not null auto_increment',
        'auto_increment null' => 'null auto_increment',
        'auto_increment invisible' => 'invisible auto_increment',
        'not null null' => 'null', // duplicity
        'not null srid 0' => 'srid 0 not null',
        'null srid 0' => 'srid 0 null',
        'column_format fixed not null' => 'not null column_format fixed',
        'storage memory not null' => 'not null storage memory',
        'storage memory column_format fixed' => 'column_format fixed storage memory',
        'storage disk column_format fixed' => 'column_format fixed storage disk',
        'storage disk column_format dynamic not null' => 'not null column_format dynamic storage disk',
        'storage memory column_format dynamic' => 'column_format dynamic storage memory',
        'storage disk storage memory' => 'storage memory', // duplicity
        'storage disk storage default' => 'storage default', // duplicity
        'storage memory storage disk' => 'storage disk', // duplicity
        'storage memory storage default' => 'storage default', // duplicity
        'column_format dynamic column_format fixed' => 'column_format fixed', // duplicity
        'column_format dynamic column_format default' => 'column_format default', // duplicity
        'column_format fixed column_format dynamic' => 'column_format dynamic', // duplicity
        'column_format fixed column_format default' => 'column_format default', // duplicity
        'column_format fixed storage disk column_format dynamic storage memory' => 'column_format dynamic storage memory', // duplicity
        'on update restrict on delete restrict' => 'on delete restrict on update restrict',
        'on update restrict on delete cascade' => 'on delete cascade on update restrict',
        'on update restrict on delete set null' => 'on delete set null on update restrict',
        'on update cascade on delete restrict' => 'on delete restrict on update cascade',
        'on update cascade on delete cascade' => 'on delete cascade on update cascade',
        'on update cascade on delete no action' => 'on delete no action on update cascade',
        'on update set null on delete set null' => 'on delete set null on update set null',
        'on update set null on delete restrict' => 'on delete restrict on update set null',
        'on update no action on delete set null' => 'on delete set null on update no action',

        // table options ordering
        'engine=innodb remove partitioning' => 'remove partitioning engine innodb',
        'engine=myisam remove partitioning' => 'remove partitioning engine myisam',
        'engine= myisam remove partitioning' => 'remove partitioning engine myisam',

        // other order
        'no sql deterministic' => 'deterministic no sql',
        'sql_calc_found_rows distinct' => 'distinct sql_calc_found_rows',
        'sql_big_result distinct' => 'distinct sql_big_result',
        'sql security definer deterministic' => 'deterministic sql security definer',
        'modifies sql data not deterministic' => 'not deterministic modifies sql data',
        'read only, with consistent snapshot' => 'with consistent snapshot, read only',
        'read write, with consistent snapshot' => 'with consistent snapshot, read write',
        'read only, isolation level read committed' => 'isolation level read committed, read only',
        'read write, isolation level read committed' => 'isolation level read committed, read write',
        'read only, isolation level repeatable read' => 'isolation level repeatable read, read only',
        'read write, isolation level repeatable read' => 'isolation level repeatable read, read write',
        'read only, isolation level serializable' => 'isolation level serializable, read only',
        'read write, isolation level serializable' => 'isolation level serializable, read write',

        '{d\'' => '{d \'',
        '{t\'' => '{t \'',
        '{ts\'' => '{ts \'',

        // [=]
        'algorithm = copy' => 'algorithm copy',
        'algorithm= copy' => 'algorithm copy',
        'algorithm=copy' => 'algorithm copy',
        'algorithm = default' => 'algorithm default',
        'algorithm= default' => 'algorithm default',
        'algorithm=default' => 'algorithm default',
        'algorithm = inplace' => 'algorithm inplace',
        'algorithm= inplace' => 'algorithm inplace',
        'algorithm=inplace' => 'algorithm inplace',
        'algorithm=instant' => 'algorithm instant',
        'algorithm = instant' => 'algorithm instant',
        'algorithm =instant' => 'algorithm instant',
        //'algorithm=undefined' => 'algorithm undefined',
        'auto_increment =' => 'auto_increment ',
        'auto_increment=' => 'auto_increment ',
        'character set =' => 'character set ',
        'character set=' => 'character set ',
        ' charset =' => ' character set ',
        ' charset=' => ' character set ',
        ') byte ' => ') character set binary ',
        ') unicode' => ') character set unicode',
        'collate =' => 'collate ',
        'collate=' => 'collate ',
        ' avg_row_length=' => ' avg_row_length ',
        'data directory=' => 'data directory ',
        //' definer =' => ' definer ',
        //' definer=' => ' definer ',
        ' delay_key_write=' => ' delay_key_write ',
        ' delay_key_write =' => ' delay_key_write ',
        'extent_size =' => 'extent_size ',
        'initial_size =' => 'initial_size ',
        'insert_method=' => 'insert_method ',
        'key_block_size =' => 'key_block_size ',
        'key_block_size=' => 'key_block_size ',
        'lock=exclusive' => 'lock exclusive',
        'lock= exclusive' => 'lock exclusive',
        'lock = exclusive' => 'lock exclusive',
        'lock=none' => 'lock none',
        'lock= none' => 'lock none',
        'lock = none' => 'lock none',
        'lock=shared' => 'lock shared',
        'lock= shared' => 'lock shared',
        'lock = shared' => 'lock shared',
        'lock=default' => 'lock default',
        'lock= default' => 'lock default',
        'lock = default' => 'lock default',
        ' insert_method =' => ' insert_method ',
        'nodegroup =' => 'nodegroup ',
        ' min_rows=' => ' min_rows ',
        ' min_rows =' => ' min_rows ',
        ' max_rows=' => ' max_rows ',
        ' max_rows =' => ' max_rows ',
        ')max_rows=' => ')max_rows ',
        'pack_keys =' => 'pack_keys ',
        'pack_keys=' => 'pack_keys ',
        'read only=' => 'read only ',
        'read only =' => 'read only ',
        ' tablespace =' => ' tablespace ',
        ' tablespace=' => ' tablespace ',
        ' undo_buffer_size =' => ' undo_buffer_size ',
        ' union=' => ' union ',
        ' union =' => ' union ',
        ' default_auth=' => ' default_auth ',
        ' comment=' => ' comment ',
        ' comment =' => ' comment ',
        ' compression=' => ' compression ',
        ' compression =' => ' compression ',
        ' connection=' => ' connection ',
        ' connection =' => ' connection ',
        ' data directory =' => ' data directory ',
        ' index directory=' => ' index directory ',
        ' index directory =' => ' index directory ',
        ' encryption=' => ' encryption ',
        ' encryption =' => ' encryption ',
        ',encryption =' => ',encryption ',
        ' engine=' => ' engine ',
        ' engine =' => ' engine ',
        ',engine =' => ',engine ',
        ')engine=' => ')engine ',
        ')engine =' => ')engine ',
        ' secondary_engine=' => ' secondary_engine ',
        ')secondary_engine=' => ')secondary_engine ',
        ' row_format=' => ' row_format ',
        ')row_format=' => ')row_format ',
        ' row_format =' => ' row_format ',
        'engine_attribute=' => ' engine_attribute ',
        ' stats_persistent=' => ' stats_persistent ',
        ' checksum=' => ' checksum ',
        ' checksum =' => ' checksum ',
        ' autoextend_size=' => ' autoextend_size ',
        ' autoextend_size =' => ' autoextend_size ',
        ' max_size =' => ' max_size ',
        ' initial_size=' => ' initial_size ',
        ' undo_buffer_size=' => ' undo_buffer_size ',
        ' stats_auto_recalc=' => ' stats_auto_recalc ',
        ',stats_auto_recalc=' => ',stats_auto_recalc ',
        ' stats_sample_pages=' => ' stats_sample_pages ',
        ' vcpu=' => ' vcpu ',
        ' thread_priority=' => ' thread_priority ',
        '(key(' => '(index(',
    ];

    /** @var array<string, string> */
    private static array $reAliases = [
        // delete .*
        '~(delete |ignore |from )([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*~' => '$1$2, $3, $4, $5',
        '~(delete |ignore |from )([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*~' => '$1$2, $3, $4',
        '~(delete |ignore |from )([a-z\d_.]+)\.\*, ?([a-z\d_.]+)\.\*~' => '$1$2, $3',
        '~(delete |ignore |from )([a-z\d_.]+)\.\*~' => '$1$2',
        // trans
        '~^begin;~' => 'start transaction;',
        // column features ordering
        '~default ([^,;(]+) not null~' => 'not null default $1',
        '~default ?\(([^)]+)\) not null~' => 'not null default ($1)',
        '~auto_increment default ?\(([^)]+)\)~' => 'default ($1) auto_increment',
        '~invisible default ([^ ]+)~' => 'default $1 invisible',
        '~invisible default\(([^)]+)\)~' => 'default($1)invisible',
        '~primary key default\(([^)]+)\)invisible~' => 'default($1)invisible primary key',
        // LIMIT OFFSET
        '~limit (\d+)[, ]+(\d+)~' => 'limit $2 offset $1',
        // hexadec & binary
        '~x\'([\da-f]+)\'~' => '0x$1',
        '~b\'([01]+)\'~' => '0b$1',
        // add whitespace after ")"
        '~\)([a-z])~' => ') $1',
        // delete from x using y vs delete x from y
        '~(?<!before |after |revoke |[a-z\d_])delete ([^(;]+) from (.+)~' => 'delete from $1 using $2',
        '~from ignore using~' => 'ignore from', // fix previous
        '~delete from quick~' => 'delete quick from',
        '~delete from ignore~' => 'delete ignore from',
        '~delete quick from ignore~' => 'delete quick ignore from',
        '~delete quick from using~' => 'delete quick from',
        '~delete quick ignore from using~' => 'delete quick ignore from',
        '~delete from low_priority using~' => 'delete low_priority from',
        '~delete from low_priority quick ignore~' => 'delete low_priority quick ignore from',
        '~delete from low_priority quick ignore using~' => 'delete low_priority quick ignore from',
        '~delete low_priority quick ignore from using~' => 'delete low_priority quick ignore from',
        // charset, but not charset()
        '~(?<![a-z\d_])charset(?![a-z\d_\(])~' => 'character set',
        // unnecessary signed
        '~(decimal|float|double)\(([^)]+)\) signed~' => '$1($2)',
        // srid
        '~not null srid (\d+)~' => 'srid $1 not null',
        // tablespace order
        '~tablespace[= ]`([^`]+)`, rename(?: to)? `([^`]+)`~' => 'rename `$2`, tablespace `$1`',
        // algo, lock order
        '~alter table ([a-z\d_]+) character set ([a-z\d]+), ([^;]+);~' => 'alter table $1 $3 character set $2;',
        '~(alter (?:online )?table) ([^ ]+) algorithm[= ](copy|inplace|default)([, ]+)?([^;]+);~' => '$1 $2 $5 algorithm $3;',
        '~(alter (?:online )?table) ([^ ]+) lock[= ](exclusive|shared|none|default)([^;]+);~' => '$1 $2 $4 lock $3;',
        // index alias
        '~,\s*key ~' => ', index ',
        '~(?<!primary |foreign |by |[a-z\d_])key \(~' => 'index (',
        // useless pk name
        '~primary key (?!check)[`a-z\d_]+ ?\(~' => 'primary key (',
        '~primary key [`a-z\d_]+ using~' => 'primary key using',
        // index vs using
        '~primary key using ([a-z]+) ?\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'primary key ($2) using $1',
        '~unique(?: index| key)? using ([a-z]+) ?\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'unique ($2) using $1',
        '~unique(?: index| key)? ([`a-z\d_]+) using ([a-z]+) ?\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'unique $1 ($3) using $2',
        '~unique(?: index| key)? ([`a-z\d_]+) using ([a-z]+) on ([^(]+)\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'unique $1 on $3($4) using $2',
        '~(?:index|key) using ([a-z]+) ?\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'index ($2) using $1',
        '~(?:index|key) ([`a-z\d_]+) using ([a-z]+) ?\(([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*)\)~' => 'index $1 ($3) using $2',
        '~(?:index|key) ([`a-z\d_]+) using ([a-z]+) on ([^(]+) ?\( ?([`a-z\d_]+(?:\(\d+\))?(?:, ?[`a-z\d_]+(?:\(\d+\))?)*) ?\)~' => 'index $1 on $3($4) using $2',
        // index vs default
        '~unique default ([^ ,;]+)~' => 'default $1 unique',
        '~primary key default ([^ ,;]+)~' => 'default $1 primary key',
        // space after collation
        '~(?<![a-z\d])_(utf8|utf8mb4|utf16(?:be|le)?|utf32|latin1|binary|koi8r|cp1251|eucjpms|ujis) (?!0)~' => '_$1',
        '~(?<!charset |character set )default collate~' => 'collate',
        // order...
        '~not null collate ([a-z\d_]+)~' => 'collate $1 not null',
        '~engine innodb, rename to ([a-z\d_]+)~' => 'rename $1, engine innodb',
        '~check ?(\([^)]+\)(?: ?(?:not )?enforced)?) ?(not null|unique|primary key)~' => '$2 check $1',
        '~default ([^ ]+) collate ([a-z\d_]+)~' => 'collate $2 default $1',
        '~alter table ([a-z\d_]+) engine (innodb|myisam)([^;]+);~' => 'alter table $1 $3 engine $2;',
        // match
        '~match ([a-z][^()]*) against~' => 'match ($1) against',
        // desc
        '~^desc ([^;]+);~' => 'describe $1;',
        // charset vs collation position
        '~collate ([a-z\d_]+) character set ([a-z\d_]+)~' => 'character set $2 collate $1',
        '~binary character set ([a-z\d_]+)~' => 'character set $1 collate binary',
        '~binary (ascii|unicode)~' => '$1 binary',
        // user
        '~(?<!where|and|or|set|,) user ?=~' => ' user ',
        '~(?<!set) password ?=~' => ' password ',
        // for update
        '~into ([^ ]+) for update~' => 'for update into $1',

        '~storage default storage default storage memory storage disk storage disk~' => 'storage disk',
        '~storage default column_format default~' => 'column_format default storage default',
    ];

    /** @var array<string, string> */
    private static array $normalize = [
        // use short versions everywhere
        'set @@session.' => 'set ',
        'set session ' => 'set ',
        ', @@session.' => ',',
        ',@@session.' => ',',
        'insert into ' => 'insert ',
        'insert ignore into ' => 'insert ignore ',
        'replace into ' => 'replace ',
        'truncate table ' => 'truncate ',
        'unlock tables' => 'unlock table',
        'drop database' => 'drop schema', // in texts
        'fetch next from ' => 'fetch ',
        'fetch from ' => 'fetch ',
        'rollback to savepoint ' => 'rollback to ',
        'inner join' => 'join',
        'left outer join' => 'left join',
        'right outer join' => 'right join',
        'fulltext index' => 'fulltext',
        'fulltext key' => 'fulltext',
        'unique index ' => 'unique ',
        'unique index,' => 'unique,',
        'unique index;' => 'unique;',
        'unique index(' => 'unique(',
        'unique index)' => 'unique)',
        'unique key ' => 'unique ',
        'unique key,' => 'unique,',
        'unique key;' => 'unique;',
        'unique key(' => 'unique(',
        'unique key)' => 'unique)',
        'spatial index' => 'spatial',
        'rename key' => 'rename index',
        'add column ' => 'add ',
        'drop column ' => 'drop ',
        'modify column ' => 'modify ',
        'change column ' => 'change ',
        'alter column ' => 'alter ',
        'rename to ' => 'rename ',
        'revoke all privileges ' => 'revoke all ',
        'generated always as(' => 'as (',
        'generated always as' => 'as',
        'member of' => 'member',
        ' and subject ' => ' subject ',
        ' and issuer ' => ' issuer ',
        ' and cipher ' => ' cipher ',
        ' as ' => ' ',
        ' as(' => ' (',

        // bare functions
        'current_user()' => 'current_user',
        '`current_user`()' => 'current_user',
        'current_user ()' => 'current_user',
        'now()' => 'current_timestamp',
        'current_timestamp()' => 'current_timestamp',
        'current_time()' => 'current_time',
        'current_date()' => 'current_date',
        'localtime()' => 'localtime',
        'localtimestamp()' => 'localtimestamp',
        'utc_date()' => 'utc_date',

        // unnecessary signed
        'tinyint signed' => 'tinyint',
        'smallint signed' => 'smallint',
        'int signed' => 'int',
        'integer signed' => 'integer',
        'bigint signed' => 'bigint',

        // add whitespace
        'timestamp\'' => 'timestamp \'',
        'time\'' => 'time \'',
        'date\'' => 'date \'',
        ';end' => '; end',

        // remove hard to check features (mandatory in some places, optional in others). this creates invalid SQL
        '`char(1)' => ' char(1)',
        "'from" => ' from',
        ',' => ' ',
        "\x08" => '\\b',
        "\x1A" => '\\z',
        '\n' => ' ',
        '\r' => ' ',
        '\t' => ' ',
        '\\' => '', // because ' in strings may have been escaped as '' and removed
        '"' => '',
        "'" => '',
        "`" => '',

        // remove non-significant whitespace
        ': begin' => ':begin',
        '    ' => ' ',
        '   ' => ' ',
        '  ' => ' ',
        ' (' => '(',
        '( ' => '(',
        ' )' => ')',
        ') ' => ')',
        '{ ' => '{',
        ' }' => '}',
        ' =' => '=',
        '= ' => '=',
        ' :=' => ':=',
        ' !=' => '!=',
        '> ' => '>',
        ' >' => '>',
        '< ' => '<',
        ' <' => '<',
        ' +' => '+',
        '+ ' => '+',
        ' -' => '-',
        '- ' => '-',
        ' *' => '*',
        '* ' => '*',
        ' /' => '/',
        '/ ' => '/',
        ' %' => '%',
        '% ' => '%',
        ' ~' => '~',
        '~ ' => '~',
        ' ^' => '^',
        '^ ' => '^',
        ' .' => '.',
        '. ' => '.',
        ' |' => '|',
        '| ' => '|',
        ' ;' => ';',

        // collate/charset shortcuts
        'collate binary' => 'binary',
        'character set ascii' => 'ascii',
        'character set unicode' => 'unicode',
        'character set binary' => 'byte',

        // random parts
        ' end $' => ' end$', // ws before delimiter
        'into ret from t1 where c1=p1' => 'from t1 where c1=p1 into ret',
        'into p1 from t1 where c1=p2' => 'from t1 where c1=p2 into p1',
        'read only 1 collate utf8mb4_0900_ai_ci' => 'collate utf8mb4_0900_ai_ci read only 1',
        'ignore 4 rows' => 'ignore 4 lines',
        'desc db_bug14533.t1' => 'describe db_bug14533.t1',
        'add index values(' => 'add index value(',
        'cannot be converted from type.*' => 'cannot be converted from type ',
        'failed to delete file.*)' => 'failed to delete file)',
        'user remote host 192.168.1.106 database test' => 'host 192.168.1.106 database test user remote',

        // crazy delimiters
        ' $$' => '$$',
        '; $' => ';$',
        '; ' => ';',

        // fix back
        'linear index' => 'linear key',
        '||user ' => '||user=',
        'user foo' => 'user=foo',
        'user bar' => 'user=bar',
        'user joe' => 'user=joe',
        'user user' => 'user=user',
        'user root' => 'user=root',
        'user illegal' => 'user=illegal',
        'user replicate' => 'user=replicate',
        'user mysql.sys' => 'user=mysql.sys',
        'user must_change' => 'user=must_change',
        'user user_name_len_22_01234' => 'user=user_name_len_22_01234',
        'user _binaryrpl_ignore_grant' => 'user=_binaryrpl_ignore_grant',
        'user wl_14281_7000' => 'user=wl_14281_7000',
        'user password_lock_both' => 'user=password_lock_both',
        'user failed_login_attempts' => 'user=failed_login_attempts',
        'user password_lock_time' => 'user=password_lock_time',
        'user password_lock_none' => 'user=password_lock_none',
        'user rpl_do_grant' => 'user=rpl_do_grant',
        'user blafasel2' => 'user=blafasel2',
        'user _binaryblafasel2' => 'user=_binaryblafasel2',
        'user _binaryrpl_do_grant' => 'user=_binaryrpl_do_grant',
        'user event_scheduler' => 'user=event_scheduler',
        'user system' => 'user=system',
        'user 9591_user' => 'user=9591_user',
        'user plug_user_p' => 'user=plug_user_p',
        'user plug_user_wp' => 'user=plug_user_wp',
        'user regular_user_p' => 'user=regular_user_p',
        'user regular_user_wp' => 'user=regular_user_wp',
        'user wl_14281_default' => 'user=wl_14281_default',
        'user wl_14281_xl' => 'user=wl_14281_xl',
        'user x_root' => 'user=x_root',
        'user%host' => 'user=%host',
        'and user%' => 'and user=%',
        'user 0' => 'user=0',
        'password proxy_user_p' => 'password=proxy_user_p',
        'password proxy_user_wp' => 'password=proxy_user_wp',
        'password 012345678901234567890123456789ab' => 'password=012345678901234567890123456789ab',
        'password password' => 'password=password',
        'password ahaha' => 'password=ahaha',
        'password secret' => 'password=secret',
        'engine=innodb' => 'engine innodb',
        'where engine ndbcluster' => 'where engine=ndbcluster',
        'and engine ndbcluster' => 'and engine=ndbcluster',
        'where engine performance_schema' => 'where engine=performance_schema',
        'set @@global.transaction' => 'set global transaction',
        'create definer ' => 'create definer=',
        'where character set like' => 'where charset like',
        'where engine myisam' => 'where engine=myisam',
        'password=password user=regular_user_p' => 'user=regular_user_p password=password', // order
        'default_auth password=password' => 'password=password default_auth',
        'max_rows @max_rows' => 'max_rows=@max_rows',
    ];

}
