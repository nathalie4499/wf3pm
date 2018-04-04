<?php

//après endif line 32 - dans addProduct.html.twig
{{ form_start(formular) }}
{{ form_row(formular.name) }}	/* {# display a label, error and input - 
                                    call three functions (before form_row: <div style="" >{{  form_errors(formular.name)}}</div> 
                                    and form_label and form_widget(formular.name) this is the input(widget) #} */
    {{ form_row(formular.description) }}
    {{ form_row(formular.version) }}
    {{ form_row(formular.submit) }}
    
 {{ form_rest(formular) }}	//{# display each element that is not already displayed #}
 {{ form_end(formular) }}	// {# insert the form ending tag #}
 
 //autre alternative
 
 {{ form_start(formular) }}
 <div style="" >{{ form_errors(formular.name) }}</div>
 {{ form_label(formular.name) }}
 {{ form_widget(formular.name) }}
 