<table border=1>
  <tr><td><b>Name</b></td>
  <td><b>Age</b></td>
  <td><b>Child Names</b></td></tr>
    <tpl:loop obj='item'>
       <tr>
         <td><tpl:name>show the name here</tpl:name></td>
         <td><tpl:years>show the years here</tpl:years></td>
         <td>
           <ul>
             <tpl:loop obj='childs'>
                 <li><tpl:name>Childs name</tpl:name></li>
             </tpl:loop>
           </ul>
         </td>
       </tr>
    </tpl:loop>
</table>

