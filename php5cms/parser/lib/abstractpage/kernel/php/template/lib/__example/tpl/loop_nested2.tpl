<table border=1>
  <tr><td><b>Name</b></td>
  <td><b>Age</b></td>
  <td><b>Child Names</b></td></tr>
    <tpl:loop obj='parent'>
       <tr>
         <td><tpl:name>show the name here</tpl:name></td>
         <td><tpl:years>show the years here</tpl:years></td>
         <td>
	   <b>John</b><br>
           <ul>
             <tpl:loop obj='john_childs'>
                 <li><tpl:name>Childs name</tpl:name></li>
             </tpl:loop>
           </ul>

           <b>Mike</b><br>
           <ul>
             <tpl:loop obj='mike_childs'> 
                 <li><tpl:name>Childs name</tpl:name></li>
             </tpl:loop>
           </ul>

           <b>Bill</b><br>
           <ul>
             <tpl:loop obj='bill_childs'> 
                 <li><tpl:name>Childs name</tpl:name></li>
             </tpl:loop>
           </ul>





         </td>
       </tr>
    </tpl:loop>
</table>

