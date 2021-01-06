

let countries = [
    { "text": "Empresa1", "value": "1" },
    { "text": "Empresa2", "value": "2" },
    { "text": "Empresa3", "value": "3" },
    
  ];
  
  
  let list = document.getElementsByClassName("list")[0];
  
  for (let i = 0; i < countries.length; i++) {
    let option = document.createElement("option");
    let text = document.createTextNode(countries[i].text);
    option.appendChild(text);
    list.appendChild(option);
  }