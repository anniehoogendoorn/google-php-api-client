<?php

    class Analytics
    {
        public $sessions_date;
        public $sessions;
        public $id;

        function __construct($sessions_date, $sessions, $id = null)
        {
            $this->sessions_date = $sessions_date;
            $this->sessions = $sessions;
            $this->id = $id;
        }

        function setSessionsDate($new_sessions_date)
        {
            $this->sessions_date = $new_sessions_date;
        }

        function getSessionsDate()
        {
            return $this->sessions_date;
        }

        function setSessions($new_sessions)
        {
            $this->sessions = $new_sessions;
        }

        function getSessions()
        {
            return $this->sessions;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO sessions (sessions_date, sessions) VALUES ('{$this->sessions_date}', '{$this->sessions}')");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll($returned_data)
        {
          $data = array();
          foreach($returned_data as $row) {
              $sessions_date = $row[0];
              $sessions = $row[1];
              $analytics_object = new Analytics($sessions_date, $sessions);
              $analytics_object->save();
              array_push($data, $analytics_object);
              // echo $sessions;
          }
          print_r ($data);

        }






    }




 ?>
