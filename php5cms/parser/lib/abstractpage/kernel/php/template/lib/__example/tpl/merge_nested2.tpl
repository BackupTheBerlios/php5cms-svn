<tpl:merge obj='parent'>
      Hello <tpl:name>show the name here</tpl:name>,<br>
      you have <b><tpl:years>show the years here</tpl:years></b>!<br>
      Lets see about your child:
       <ul>
         <tpl:merge obj='child'>
           <li>Name: <tpl:name>child's name</tpl:name>
           <li>Age:  <tpl:years>child's age</tpl:years>
         </tpl:merge>
       </ul>
</tpl:merge>

