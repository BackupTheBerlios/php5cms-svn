#################################################################################
#
# createAccount procedure
#
# DESCRIPTION:
# Creates a new user account and return it's ID.
#
# NOTE:
# UserPassword parameter is stored hashed
#
# Parameters:
# @UserName       VARCHAR
# @UserPassword   VARCHAR
# @FirstName      VARCHAR
# @LastName       VARCHAR
#
# Return Value:
# UserID          BIGINT
#
#################################################################################

#Set parameters
SET @UserName = '<param name="UserName"/>';
SET @UserPassword = '<param name="UserPassword"/>';
SET @FirstName = '<param name="FirstName"/>';
SET @LastName = '<param name="LastName"/>';

#Select the DB to be used
USE junk;

#Start transaction (requires innoDB or BDB tables)
BEGIN;

#Do action
INSERT INTO
    users
        (
            UserName,
            UserPassword
        )
    VALUES
        (
            @UserName,
            PASSWORD(@UserPassword)
        )
;

#Remember the new UserID
SELECT @UserID := LAST_INSERT_ID();

#Insert details record
INSERT INTO
    user_details
                (
                    UserID,
                    FirstName,
                    LastName
                )
    VALUES
                (
                    @UserID,
                    @FirstName,
                    @LastName
                )
;

#Commit the transaction (if an error occured before this point transaction is auto rolled back)
COMMIT;

#Return the new UserID created
SELECT @UserID AS UserID;
