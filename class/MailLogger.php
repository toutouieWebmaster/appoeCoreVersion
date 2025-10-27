<?php

namespace App;

class MailLogger
{
    private string $tableName = '`' . TABLEPREFIX . 'appoe_mailLogger`';
    private string $date;
    private string $ip;
    private string $source;
    private string $object;
    private string $toEmail;
    private string $toName;
    private string $fromEmail;
    private string $fromName;
    private string $message;

    public bool $sent = true;

    public function __construct(array $data = [])
    {
        if (!isArrayEmpty($data)) {
            $this->date = date('Y-m-d H:i:s');
            $this->ip = getIP() ?: '';
            $this->source = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['HTTP_HOST'];

            $this->object = $data['object'];
            $this->toEmail = $data['toEmail'];
            $this->toName = $data['toName'];
            $this->fromEmail = $data['fromEmail'];
            $this->fromName = $data['fromName'];
            $this->message = $data['message'];
        }
    }

    public function createTable(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `date` datetime NOT NULL,
                `object` varchar(70) NOT NULL,
                `toEmail` varchar(70) NOT NULL,
                `toName` varchar(70) NOT NULL,
                `fromEmail` varchar(70) NOT NULL,
                `fromName` varchar(70) NOT NULL,
                `message` text,
                `ip` varchar(50) DEFAULT NULL,
                `source` varchar(255) DEFAULT NULL,
                `sent` tinyint(1) NOT NULL DEFAULT "1",
                UNIQUE (`date`, `object`, `fromEmail`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return (bool)DB::exec($sql);
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $sql = 'INSERT INTO ' . $this->tableName . ' (`date`, `object`, `toEmail`, `toName`, `fromEmail`, `fromName`, `message`, `ip`, `source`, `sent`) 
                VALUES (:date, :object, :toEmail, :toName, :fromEmail, :fromName, :message, :ip, :source, :sent)';
        $params = array(
            ':date' => $this->date,
            ':object' => $this->object,
            ':toEmail' => $this->toEmail,
            ':toName' => $this->toName,
            ':fromEmail' => $this->fromEmail,
            ':fromName' => $this->fromName,
            ':message' => $this->message,
            ':ip' => $this->ip,
            ':source' => $this->source,
            ':sent' => $this->sent
        );
        if (DB::exec($sql, $params)) {
            appLog('Logging mail -> date: ' . $this->date . ' subject:' . $this->object);
            return true;
        }
        return false;
    }


    /**
     * @param ?int $limit
     *
     * @return array|object|bool
     */
    public function showAll(?int $limit = null): array|object|bool
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' ORDER BY `date` DESC';
        $sql .= $limit ? ' LIMIT ' . $limit : '';
        $result = DB::exec($sql);
        return $result ? $result->fetchAll(\PDO::FETCH_OBJ) : false;
    }
}