var cy = window.cy = cytoscape({
  container: document.getElementById('cy'),

  boxSelectionEnabled: false,
  autounselectify: true,

  style: [
    {
      selector: 'node',
      css: {
        'content': 'data(id)',
        'text-valign': 'center',
        'text-halign': 'center',
        'shape': 'roundrectangle',
        'width': '50px',
        'background-color': 'orange' 
      }
    },

    // {
    //   selector: '#CPSC222',
    //   css: {
    //     'padding-top': '10px',
    //     'padding-left': '10px',
    //     'padding-bottom': '10px',
    //     'padding-right': '10px'
    //   }
    // },
    // {
    //   selector: '#CPSC340',
    //   css: {
    //     'padding-top': '10px',
    //     'padding-left': '10px',
    //     'padding-bottom': '10px',
    //     'padding-right': '10px'
    //   }
    // },
    {
      selector: '$node > node', //container
      css: {
        'padding-top': '10px',
        'padding-left': '10px',
        'padding-bottom': '10px',
        'padding-right': '10px',
        'text-valign': 'top',
        'text-halign': 'center',
        'background-color': 'blue'
      }
    },
    {
      selector: 'edge',
      css: {
        'target-arrow-shape': 'triangle'
      }
    },
    {
      selector: ':selected',
      css: {
        'background-color': 'black',
        'line-color': 'black',
        'target-arrow-color': 'black',
        'source-arrow-color': 'black'
      }
    }
  ],

  // elements: {
  //   nodes: [
  //     { data: { id: 'a', parent: 'b' }, position: { x: 215, y: 85 } },
  //     { data: { id: 'b' } },
  //     { data: { id: 'c', parent: 'b' }, position: { x: 300, y: 85 } },
  //     { data: { id: 'd' }, position: { x: 215, y: 175 } },
  //     { data: { id: 'e' } },
  //     { data: { id: 'f', parent: 'e' }, position: { x: 300, y: 175 } }
  //   ],
  //   edges: [
  //     { data: { id: 'ad', source: 'a', target: 'd' } },
  //     { data: { id: 'eb', source: 'e', target: 'b' } }

  //   ]
  // },

  layout: {
    name: 'preset',
    padding: 5
  }
});

cy.add({ data: { id: 'CPSC221', credit: 4 } }, position: { x: 215, y: 85 });
cy.add({ data: { id: 'CPSC340', credit: 4 } });
cy.add({ data: { id: 'cs', source: 'CPSC221', target: 'CPSC340' } });