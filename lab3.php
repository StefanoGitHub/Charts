<?php
//lab3.php
//http://phpanddb.netne.net/lab3.php

define("THIS_PAGE", basename($_SERVER['PHP_SELF']));

session_start();

//END CONFIG AREA ----------------------------------------------------------

if (isset($_POST['Archive']) )
{//create book   
    
    $invalidInput = ''; 

    $title = $_POST['Title'];
    $author = $_POST['Author'];
    $category = $_POST['Category'];
    $price = $_POST['Price'];


    if(isset($_SESSION['Books']))
    {//resume the previous book archive if exists
        $books = $_SESSION['Books'];
    }
    if (validInput())  
    {//if the input is valid ad the book to the list
        $books[] = new Book($title, $author, $category, $price);
    }else
    {//otherwise send a message
        $invalidInput = 'Please enter valid data';
    }   


    $totalValue = 0; //will store the total value of the archived books
    foreach($books as $book)
    {
        $totalValue += $book->Price; //adding the price of all books
    }//end foreach


    $count = count($books); //number of archived books
    $average = ($count != 0) ? number_format($totalValue/$count, 2) : 0;   //average=0 if count=0 



    $_SESSION['Books'] = $books;
    
}

if (isset($_POST['Clear']))
{//clear data and session
    unset($books);
    session_destroy(); 
} 


echo '
    <form action="'. THIS_PAGE .'" method="post">
		<table align="center">
			<tr>
				<td align="right"> Title:<b style="font-size:0.6em;">*</b></td>      <td><input type="text" name="Title" /></td>
			</tr>
            <tr>
				<td align="right"> Author:<b style="font-size:0.6em;">*</b></td>     <td><input type="text" name="Author" /></td>
			</tr>
            <tr>
				<td align="right"> Category:<b style="font-size:0.6em;">*</b></td>   <td><input type="text" name="Category" /></td>
			</tr>
            <tr>
				<td align="right"> Price:<b style="font-size:0.6em;">**</b></td>     <td><input type="text" name="Price" /></td>
			</tr>
            <tr>
                <td align="center"> <input type="submit" name="Archive" value="Archive" > </td>
                <td align="center">'. $invalidInput .'</td>
            </tr>

		</table>
	</form>
    <div align="center" style="font-size:0.8em;">
        <em>*</em> alphabetic only <br>
        <em>**</em> numeric only
    </div>

    <br>

    <table align="center">
        <tr>
            <td> <form action="'. THIS_PAGE .'" method="post">
                    <input type="submit" name="Clear" value="Clear library!" >
                </form> </td>
        </tr>
    </table>
    
';                    
    
    $i=1;

    if (isset($books)) 
    { //if there is at least one book summarize and print the list
        
        echo '
            <br><br>
            <h3 align="center">You have a total of '. $count .' books for a total value of $'. $totalValue 
                                                        .', which averages to $'. $average .' per book</h3>
            ';
        
        foreach($books as $book)
        {// print the values in the archive

            $price = number_format($book->Price, 2);
            echo "
                <p align=\"center\"> $i. <b>$book->Title</b>, <i>by $book->Author</i> &nbsp;|&nbsp; $book->Category  [\$$price]</p>
            ";
            //increment the book count
            $i++;

        }//end foreach  
    }//end if     
    else 
    {//if there are no books write a message
        echo '
            <br><br>
            <h3 align="center">You have a no books in your library! :(</h3>
            ';
    }//enf else


//function definition
function validInput() {
    
    if (preg_replace('/\s+/', '', $_POST['Title']) != '' &&
        preg_replace('/\s+/', '', $_POST['Author']) != '' &&
        preg_replace('/\s+/', '', $_POST['Category']) != '' &&
        is_numeric($_POST['Price']) &&
        $_POST['Price'] >= 0
       )
    {
        return true;
    }
    else 
    {
        return false;
    }
    
}

//class definition
class Book{
    
    public $Title = ''; //preloaded safe init value
    public $Author = '';
    public $Category = '';
    public $Price = 0;
        
    // php constructor
    public function __construct($Title, $Author, $Category, $Price) 
    {
        $this->Title = $Title;
        $this->Author = $Author;
        $this->Category = $Category;
        $this->Price = $Price;
    }//end constructor
    

    public function __toString()
    {
        
        setlocale(LC_MONETARY, 'en_US');
        $Allowance = money_format('%(#10n', $this->Allowance);
        
        $myReturn = '';
        
        $myReturn .= "Name: " . $this->Name . " | ";
        $myReturn .= "Hobby: " . $this->Hobby . " | ";
        $myReturn .= "Allowance: " . $Allowance ;
        
        
        return $myReturn;
    }//end toString()
    
} //end Book()




