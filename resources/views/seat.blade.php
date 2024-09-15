<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
  <div class="max-w-7xl mx-auto p-8">
    <form onsubmit="return false" class="space-y-4">
      <div>
        <label class="block">
          <span class="block text-sm font-medium">Tên sơ đồ ghế</span>
          <input type="text" class="input input-sm input-bordered input-primary" />
        </label>
      </div>
      <div id="seatingArea" class="flex items-center mb-3">
        <div id="rowLabels" class="flex flex-col gap-2"></div>
        <div id="draggableTable" class="grid gap-2"></div>
        <div id="contextMenu" class="context-menu"></div>
      </div>
      <div class="mb-3">
        <button class="btn btn-sm btn-info">Chọn từ sơ đồ ghế có sẵn</button>
      </div>
      <div class="mb-3">
        <button class="btn btn-sm btn-primary">Lưu</button>
      </div>
    </form>

  </div>
</body>
<script type="module">
  let apiResponse = {};
  const seatTypes = {
    "empty-seat": {
      text: "Xóa",
      color: "#000000",
      icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-x-circle' viewBox='0 0 16 16'><path d='M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z'/><path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/></svg>"
    },
    "standard-seat": {
      text: "Ghế mặc định",
      color: "#FFFFFF",
      icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person' viewBox='0 0 16 16'><path d='M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM2 14s-1 0-1-1 1-4 7-4 7 3 7 4-1 1-1 1H2z'/></svg>"
    },
    "couple-seat": {
      text: "Ghế cặp",
      color: "#FFB6C1",
      icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-heart' viewBox='0 0 16 16'><path d='M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.414-2.368 5.327-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01z'/></svg>"
    },
    "vip-seat": {
      text: "Ghế VIP",
      color: "#FFD700",
      icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-star' viewBox='0 0 16 16'><path d='M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z'/></svg>"
    },
    "accessible-seat": {
      text: "Ghế cho người khuyết tật",
      color: "#32CD32",
      icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-wheelchair' viewBox='0 0 16 16'><path d='M12 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m-.663 2.146a1.5 1.5 0 0 0-.47-2.115l-2.5-1.508a1.5 1.5 0 0 0-1.676.086l-2.329 1.75a.866.866 0 0 0 1.051 1.375L7.361 3.37l.922.71-2.038 2.445A4.73 4.73 0 0 0 2.628 7.67l1.064 1.065a3.25 3.25 0 0 1 4.574 4.574l1.064 1.063a4.73 4.73 0 0 0 1.09-3.998l1.043-.292-.187 2.991a.872.872 0 1 0 1.741.098l.206-4.121A1 1 0 0 0 12.224 8h-2.79zM3.023 9.48a3.25 3.25 0 0 0 4.496 4.496l1.077 1.077a4.75 4.75 0 0 1-6.65-6.65z'/></svg>"
    }
  }

  function generateSeats({
    seats,
    col_count
  }) {
    const table = $('#draggableTable').empty().removeClass().addClass(`grid gap-2 grid-cols-${col_count}`);
    const rowLabels = $('#rowLabels').empty();

    const rows = seats.length / col_count;
    for (let i = 0; i < rows; i++) {
      const rowSeats = seats.slice(i * col_count, (i + 1) * col_count);
      rowLabels.append($('<div>', {
        class: 'row-label row-label-lg mr-5',
        text: String.fromCharCode(65 + i)
      }));
    }


    seats.forEach((seat, index) => {
      table.append($('<div>', {
        class: `draggable seat seat-lg col-span-${seat.slot} bg-${seat.type} ${!seat.visible ? 'hidden' : ''}`,
        id: seat.seat_number,
        draggable: true,
        'data-slot': seat.slot,
        'data-type': seat.type,
        'data-seat-number': seat.seat_number,
        'data-visible': seat.visible,
      }));
      $(table.children()[index]).data('merged-seats', seat.merged_seats ?? []);
    });



    let seatIndex = 0;
    $('#draggableTable > div').each(function(i, cell) {
      const rowIndex = Math.floor(i / col_count);
      $(cell).html(`${String.fromCharCode(65 + rowIndex)}${(seatIndex + 1).toString().padStart(2, '0')}`);
      $(cell).data('seat-number', `${String.fromCharCode(65 + rowIndex)}${(seatIndex + 1).toString().padStart(2, '0')}`);
      seatIndex++;
      if (seatIndex >= col_count) {
        seatIndex = 0;
      }

    });


    $('#draggableTable > div').each(function() {
      addEventListeners($(this));
    });


    return getSeatsFromDOM();
  }

  function getSeatsFromDOM() {
    const seats = [];
    $('#draggableTable > div').each(function() {
      const seat = {
        type: $(this).data('type'),
        slot: $(this).data('slot'),
        sort: $(this).index(),
        visible: $(this).data('visible'),
        seat_number: $(this).data('seat-number'),
        merged_seats: $(this).data('merged-seats')
      };
      seats.push(seat);
    });
    return seats;
  }

  let draggedElement = null;
  let contextElement = null;

  function addEventListeners(draggable) {
    draggable.on('dragstart', (e) => {
      draggedElement = e.target;
      e.originalEvent.dataTransfer.effectAllowed = 'move';
      e.originalEvent.dataTransfer.setData('text/plain', $(e.target).html());
    });

    draggable.on('dragover', (e) => {
      e.preventDefault();
      $('#draggableTable > div').removeClass('drag-over');
      if (draggedElement !== e.target) {
        const draggedData = $(draggedElement).data();
        const targetIndex = $(e.target).index();
        const slot = draggedData.slot;
        const rowLength = apiResponse.col_count;

        if ((targetIndex % rowLength) + slot <= rowLength) {
          let validDrop = true;
          let sumSlot = 0;

          for (let i = 0; i < slot; i++) {
            const targetCell = $(e.target).parent().children().eq(targetIndex + i);
            sumSlot += targetCell.data('slot');
            if (sumSlot > slot) {
              validDrop = false;
              break;
            }
          }

          if (validDrop) {
            for (let i = 0; i < slot; i++) {
              const targetCell = $(e.target).parent().children().eq(targetIndex + i);
              targetCell.addClass('drag-over');
            }
            e.originalEvent.dataTransfer.dropEffect = 'move';
            $(e.target).css('cursor', 'copy');
          } else {
            e.originalEvent.dataTransfer.dropEffect = 'none';
            $(e.target).css('cursor', 'not-allowed');
          }
        } else {
          e.originalEvent.dataTransfer.dropEffect = 'none';
          $(e.target).css('cursor', 'not-allowed');
        }
      }
    });

    draggable.on('dragleave', (e) => {
      $('#draggableTable > div').removeClass('drag-over');
      $(e.target).css('cursor', '');
    });

    draggable.on('drop', (e) => {
      e.preventDefault();
      $('#draggableTable > div').removeClass('drag-over');
      $(e.target).css('cursor', '');
      if (draggedElement !== e.target) {
        const draggedData = {
          slot: $(draggedElement).data('slot'),
          type: $(draggedElement).data('type'),
          seatNumber: $(draggedElement).data('seat-number'),
          visible: $(draggedElement).data('visible'),
          mergedSeats: $(draggedElement).data('merged-seats') || []
        };

        const targetIndex = $(e.target).index();
        const slot = draggedData.slot;
        const rowLength = apiResponse.col_count;


        if ((targetIndex % rowLength) + slot <= rowLength) {
          let validDrop = true;
          let sumSlot = 0;

          for (let i = 0; i < slot; i++) {
            const targetCell = $(e.target).parent().children().eq(targetIndex + i);
            sumSlot += targetCell.data('slot');
            if (sumSlot > slot) {
              validDrop = false;
              break;
            }
          }

          if (validDrop) {
            const targetData = {
              slot: $(e.target).data('slot'),
              type: $(e.target).data('type'),
              seatNumber: $(e.target).data('seat-number'),
              visible: $(e.target).data('visible'),
              mergedSeats: $(e.target).data('merged-seats') || []
            };


            draggedData.mergedSeats.forEach(seatId => {
              $(`#${seatId}`).data('visible', true);
            });


            $(draggedElement).data('merged-seats', []);
            const newMergedSeats = [];
            for (let i = 0; i < slot; i++) {
              const targetCell = $(e.target).parent().children().eq(targetIndex + i);
              targetCell.data('visible', false);
              if (i !== 0) {
                newMergedSeats.push(targetCell.data('seat-number'));
              }
            }
            $(e.target).data('merged-seats', newMergedSeats);


            $(draggedElement).data('slot', targetData.slot);
            $(draggedElement).data('type', targetData.type);
            $(draggedElement).data('seat-number', targetData.seatNumber);
            $(draggedElement).data('visible', targetData.visible);

            $(e.target).data('slot', draggedData.slot);
            $(e.target).data('type', draggedData.type);
            $(e.target).data('seat-number', draggedData.seatNumber);
            $(e.target).data('visible', draggedData.visible);

            apiResponse.seats = getSeatsFromDOM();
            generateSeats(apiResponse);
          }
        }
      }
    });

    draggable.on('contextmenu', (e) => {
      e.preventDefault();
      contextElement = e.target;
      showContextMenu(e.pageX, e.pageY);
    });
  }

  function showContextMenu(x, y) {
    const menu = $('#contextMenu');
    const rowIndex = $(contextElement).index() / apiResponse.col_count;
    const colIndex = $(contextElement).index() % apiResponse.col_count;
    const slot = $(contextElement).data('slot');
    const type = $(contextElement).data('type');
    const isFirstCol = colIndex === 0;
    const isLastCol = colIndex === apiResponse.col_count - 1;
    const menuItems = [
      !isFirstCol && {
        action: () => mergeSeats('merge-left'),
        text: 'Hợp nhất bên trái',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M5.854 4.146a.5.5 0 0 1 0 .708L2.707 8H14.5a.5.5 0 0 1 0 1H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0z"/></svg>',
        color: '#FF0000'
      },
      !isLastCol && {
        action: () => mergeSeats('merge-right'),
        text: 'Hợp nhất bên phải',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M10.146 4.146a.5.5 0 0 1 .708 0l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 9H1.5a.5.5 0 0 1 0-1h11.793L10.146 4.854a.5.5 0 0 1 0-.708z"/></svg>',
        color: '#00FF00'
      },
      slot !== 1 && {
        action: () => splitSeat(),
        text: 'Tách ra',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-arrows-split" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8 1.5a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 .708-.708L7.5 7.793V2a.5.5 0 0 1 .5-.5zm0 13a.5.5 0 0 1-.5-.5v-5.793l-2.146 2.147a.5.5 0 0 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 8.707V14a.5.5 0 0 1-.5.5z"/></svg>',
        color: '#FF0000'
      },
      type !== 'empty-seat' && {
        action: () => convertToEmptySeat(),
        text: 'Xóa',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16"><path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/></svg>',
        color: '#FFA500'
      },
      {
        text: 'Chọn loại ghế',
        icon: '',
        color: '#FFFFFF',
        dropdown: Object.keys(seatTypes)
          .filter(type => type !== 'empty-seat' && type !== 'couple-seat')
          .map(type => ({
            action: () => convertTo(type),
            text: seatTypes[type].text,
            icon: seatTypes[type].icon,
            color: seatTypes[type].color
          }))
      }
    ].filter(item => item);
    createContextMenu(menuItems);

    menu.css({
      left: `${x}px`,
      top: `${y}px`,
      display: 'block'
    });
  }

  function hideContextMenu() {
    $('#contextMenu').hide();
  }

  $(document).on('click', (e) => {
    if (!$(e.target).closest('.context-menu').length) {
      hideContextMenu();
    }
  });

  function mergeSeats(action) {
    let elMergeable = null;
    if (action === 'merge-left') {
      elMergeable = $(contextElement).prev();
      while (!elMergeable.data('visible')) {
        elMergeable = elMergeable.prev();
      }
    } else if (action === 'merge-right') {
      elMergeable = $(contextElement).next();
      while (!elMergeable.data('visible')) {
        elMergeable = elMergeable.next();
      }
    }
    if (elMergeable) {
      const oldSlot = $(contextElement).data('slot');
      const newSlot = parseInt(oldSlot) + parseInt(elMergeable.data('slot'));
      $(contextElement).data('slot', newSlot).data('type', 'couple-seat');
      $(elMergeable).data('visible', false);
      $(contextElement).data('merged-seats').push($(elMergeable)[0].id)
    }
  }

  function convertToEmptySeat() {
    const slots = parseInt($(contextElement).data('slot'));
    $(contextElement).data('slot', 1).data('type', 'empty-seat');
    let nextElement = $(contextElement).next();
    while (!nextElement.data('visible')) {
      nextElement.data('visible', true).data('type', 'empty-seat').data('slot', 1);
      nextElement = nextElement.next();
    }
    let prevElement = $(contextElement).prev();
    while (!prevElement.data('visible')) {
      console.log('cc');
      prevElement.data('visible', true).data('type', 'empty-seat').data('slot', 1);
      prevElement = prevElement.next();
    }
  }

  function splitSeat() {
    const slots = parseInt($(contextElement).data('slot'));
    $(contextElement).data('slot', 1);
    if ($(contextElement).hasClass('couple-seat')) {
      $(contextElement).data('slot', 1).data('type', 'standard-seat');
    }
    let nextElement = $(contextElement).next();
    while (!nextElement.data('visible')) {
      nextElement.data('visible', 'show').data('slot', 1);
      nextElement = nextElement.next();
    }
    let prevElement = $(contextElement).prev();
    while (!prevElement.data('visible')) {
      prevElement.data('visible', 'show').data('slot', 1);
      prevElement = prevElement.next();
    }
  }

  function convertTo(type) {
    $(contextElement).data('type', type);
  }

  function createContextMenu(menuItems) {
    const menu = $('#contextMenu').empty();
    menuItems.forEach(item => {
      const menuItem = $('<div>', {
        class: 'context-menu__item',
        click: function() {
          if (item.action) {
            item.action();
            apiResponse.seats = getSeatsFromDOM();
            apiResponse.seats = generateSeats(apiResponse);
            hideContextMenu();
          }
        }
      });
      const icon = $(item.icon).css('color', item.color);
      menuItem.append(icon).append(item.text);

      if (item.dropdown) {
        const dropdownMenu = $('<div>', {
          class: 'context-menu__dropdown'
        });
        item.dropdown.forEach(subItem => {
          const dropdownItem = $('<div>', {
            class: 'context-menu__dropdown-item',
            click: function() {
              subItem.action();
              apiResponse.seats = getSeatsFromDOM();
              apiResponse.seats = generateSeats(apiResponse);
              hideContextMenu();
            }
          });
          const subIcon = $(subItem.icon).css('color', subItem.color);
          dropdownItem.append(subIcon).append(subItem.text);
          dropdownMenu.append(dropdownItem);
        });
        menuItem.append(dropdownMenu);
      }

      menu.append(menuItem);
    });
  }


  async function captureElementToImage($element) {
    return new Promise((resolve, reject) => {
      domtoimage.toPng($element[0])
        .then((dataUrl) => {
          resolve(dataUrl);
        })
        .catch((error) => {
          console.error('dom-to-image failed', error);
          reject(error);
        });
    });
  }

  $(document).ready(async function() {
    const response = await axios.get('http://bookmon.test/api/seat-layouts/1');
    console.log(response.data);
    apiResponse = response.data;
    generateSeats(apiResponse);
    $('#draggableTable > div').each(function() {
      addEventListeners($(this));
    });
  });

  function dataUrlToBlob(dataUrl) {
    const byteString = atob(dataUrl.split(',')[1]);
    const mimeString = dataUrl.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);

    for (let i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ab], {
      type: mimeString
    });
  }

  function downloadBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }
</script>

</html>