<?php

    class DbHandler {
        private $conn;
        public $error;
        public $lastInsertedID;

        public function __construct($dbName, $username, $pass, $serverAdress, $dbType) {
            $this->conn = new PDO("$dbType:host=$serverAdress;dbname=$dbName", $username, $pass);

            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function CreateData($sql) {

            try {

                // use exec() because no results are returned
                $this->conn->exec($sql);

                $lastInsertedID = $this->conn->lastInsertId();
                $return = TRUE;

            } catch(PDOException $e) {
                // enable line herunder for debugging
                $return = $sql . "<br>" . $e->getMessage();
            }

            return $return;
        }

        public function ReadData($sql) {

            try {
                $localConn = $this->conn->prepare($sql);
                $localConn->execute();

                // Get an associative array
                $return = $localConn->fetchAll(PDO::FETCH_ASSOC);
            }

            catch(PDOException $e) {
                throw new Exception("SQL: $sql ERROR: ", $e->getMessage() );
                $return = false;
            }

            return $return;
        }

        public function UpdateData($sql) {

            try {

                // Prepare statement
                $localConn = $this->conn->prepare($sql);

                // execute the query
                $localConn->execute();

                //Return Parts
                $lastInsertedID = $this->conn->lastInsertId();
                $return = TRUE;

            } catch(PDOException $e) {
                throw new Exception("SQL: $sql ERROR: ", $e->getMessage() );
                $return = false;
            }

            return $return;
        }

        public function DeleteData($sql) {

            try {
                // use exec() because no results are returned
                $this->conn->exec($sql);

                $return = TRUE;

            } catch(PDOException $e) {
                // enable line herunder for debugging
                throw new Exception("SQL: $sql ERROR: ", $e->getMessage() );
                $return = false;
            }

            return $return;
        }

        public function DB_getResultCount($tableName) {
            try {
                $localConn->$this->conn;
                $localConn->prepare("SELECT count(*) FROM $tableName");
                $localConn->execute();
                return $localConn->fetchColumn();

            } catch(PDOException $e) {
                throw new Exception("TABLENAME: $tablename ERROR: ", $e->getMessage() );
                return false;
            }
        }

        public function GetCollumnNames($tablename) {
            try {
                $localConn = $this->conn->prepare("SELECT * FROM $tablename LIMIT 0, 1");
                $localConn->execute();
                $queryRes = $localConn->fetch(PDO::FETCH_ASSOC);
            }

            catch(PDOException $e) {
                throw new Exception("TABLENAME: $tablename ERROR: ", $e->getMessage() );
                return false;
            }

            $data = [];
            $i = 0;
            foreach ($queryRes as $key => $value) {
                $data[$i] = $key;
                $i++;
            }

            return $data;
        }

        public function SelectWithCodeFromArray($array, $code) {
            $splittedCode = str_split($code);
            $return = []; // <--- is used to store the output data
            $y=0; // <--- is used to count in which position the next datapiece needs to go

            for ($i=0; $i<count($array); $i++) {
                if ($splittedCode[$i] == 0) {

                }
                else if ($splittedCode[$i] == 1) {
                    $return[$y] = $array[$i];
                    $y++;
                }
                else if ($splittedCode[$i] == 2) {
                    //runs till the end of the array and writes everything inside the array
                    for ($i=$i; $i<count($array); $i++) {
                        $return[$y] = $array[$i];
                        $y++;
                    }
                }
                else if ($splittedCode[$i] == 3) {
                    //runs till the end of the array and writes nothings
                    for ($i=$i; $i<count($array); $i++) {

                    }
                }
            }
            return $return;
        }

        private function ExtractData($inputColumnNames, $inputAssocArray) {
            // extract data from $inputAssocArray with provided columnNames
            $sqlAssocArray = [];
            for ($i=0; $i<count($inputColumnNames); $i++) {
                $sqlAssocArray[$i] = $inputAssocArray[$inputColumnNames[$i] ];
            }
            return $sqlAssocArray;
        }

        private function TestIfEmpty($inputColumnNames, $sqlAssocArray) {
            // tests if all fields are filled
            $emptyTest = "true";
            for ($i=0; $i<count($inputColumnNames); $i++) {
                if ($sqlAssocArray[$i] == "") {
                    $emptyTest = "false";
                }
            }
            return $emptyTest;
        }

        private function GenerateSqlColumnNames($inputColumnNames) {
            //Generates $sqlColumnNames
            $sqlColumnNames = $inputColumnNames[0];
            for ($i=1; $i<count($inputColumnNames); $i++) {
                $sqlColumnNames .= "," . $inputColumnNames[$i];
            }
            return $sqlColumnNames;
        }

        private function SetRecordData($sqlArray, $inputColumnNames) {
            //Adds datafields to records till the last datafield is reached
            $recordData = "'" . $sqlArray[0] . "'";
            for ($i=1; $i<count($inputColumnNames); $i++) {
                $recordData .= "," . "'" . $sqlArray[$i] . "'";
            }
            return $recordData;
        }

        private function SetInsertQuery($tableName, $sqlColumnNames, $recordData) {
            //Combines $recordData, $tableName and $sqlColumnNames to create the SQL query
            $sql = "INSERT INTO $tableName ($sqlColumnNames)
            VALUES ($recordData)";

            return $sql;
        }

        public function InsertIntoDatabase($tableName, $inputColumnNames, $inputAssocArray) {

            ###
            # active code InsertIntoDatabase functionF

            // set numbered array and set $emptytest
            $sqlArray = $this->ExtractData($inputColumnNames, $inputAssocArray);
            $emptyTest = $this->TestIfEmpty($inputColumnNames, $sqlArray);

            //if tests where succesfull create sql query
            if ($emptyTest == 'true') {

                // set the SQL Query
                $sqlColumnNames = $this->GenerateSqlColumnNames($inputColumnNames);
                $recordData = $this->SetRecordData($sqlArray, $inputColumnNames);
                $sql = $this->SetInsertQuery($tableName, $sqlColumnNames, $recordData);

                // try to add the record with pdo to the database
                try {
                    // use exec() because no results are returned
                    $this->conn->exec($sql);

                    $this->lastInsertedID = $this->conn->lastInsertId();
                    $return = TRUE;

                } catch(PDOException $e) {
                    /*enable line herunder for debugging*/
                    $return = "?alert=" . $sql . "<br>" . $e->getMessage();
                }
                $conn = null;

            //if not all fields are returbn a getString that not all fields are filled
            } if ($emptyTest != 'true') {
                $return = "?alert=Fill in the whole form";
            }

            //return true or a $_GET string with the error;
            return $return;
        }


        public function __Destruct() {
            $this->conn = null;
        }
    }
 ?>
