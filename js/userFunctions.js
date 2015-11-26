
	 function registerNew() {
                console.log("Called registerNew function");
                var formData = {};
                $('#regForm').find('input[name]').each(function (index, node) {
                    formData[node.name] = node.value;
                });
                console.log(formData);
                registerNewAjax(formData);
               
                event.preventDefault(); //preventing default get request

            };


