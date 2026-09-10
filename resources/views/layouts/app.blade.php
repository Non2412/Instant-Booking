<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'จองทันใจ · จองโต๊ะร้านอาหารและสนามกีฬา')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Thai:wght@500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --forest:#1F3327;
    --forest-2:#2B4638;
    --ivory:#F8F4EA;
    --panel:#FFFFFF;
    --line:#E6DFCE;
    --gold:#C0923A;
    --gold-dark:#9C7628;
    --rust:#A2483C;
    --rust-bg:#F6E5E1;
    --blue:#2F6480;
    --blue-bg:#E4EDF0;
    --green-ok:#3F7A52;
    --green-ok-bg:#E4F0E6;
    --text:#20261D;
    --muted:#736C5A;
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body{
    font-family:'Noto Sans Thai',sans-serif;
    background:var(--ivory);
    color:var(--text);
    -webkit-font-smoothing:antialiased;
    min-height:100vh;
    display:flex;
    flex-direction:column;
  }
  h1,h2,h3,.serif{font-family:'Noto Serif Thai',serif;}
  .mono{font-family:'JetBrains Mono',monospace;}
  a{color:inherit; text-decoration:none;}
  .screen{display:none;}
  .screen.active{display:block;}
  button{cursor:pointer; font-family:inherit;}

  /* ---- shared ---- */
  .wrap{max-width:1120px; margin:0 auto; padding:0 32px; width:100%;}
  .btn{display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 22px; border-radius:999px; border:1px solid transparent; font-size:14.5px; font-weight:600; transition:all .18s ease;}
  .btn-gold{background:var(--gold); color:#fff;}
  .btn-gold:hover{background:var(--gold-dark); transform:translateY(-1px);}
  .btn-gold:active{transform:translateY(0);}
  .btn-outline{background:transparent; border-color:var(--forest); color:var(--forest);}
  .btn-outline:hover{background:var(--forest); color:#fff;}
  .btn-ghost{background:transparent; border:none; color:var(--forest); font-weight:600; padding:6px 12px; border-radius:8px;}
  .btn-ghost:hover{background:rgba(31,51,39,0.06);}
  .btn-sm{padding:8px 16px; font-size:13px;}
  .btn-xs{padding:4px 10px; font-size:11.5px; border-radius:6px;}
  .pill{display:inline-flex; align-items:center; gap:5px; font-size:11.5px; padding:4px 11px; border-radius:999px; font-weight:600;}
  .pill.ok{background:var(--green-ok-bg); color:var(--green-ok);}
  .pill.warn{background:#FBF0DC; color:var(--gold-dark);}
  .pill.no{background:var(--rust-bg); color:var(--rust);}
  .pill.info{background:var(--blue-bg); color:var(--blue);}
  .pill::before{content:''; width:5px; height:5px; border-radius:50%; background:currentColor;}
  .tag{font-size:12px; padding:4px 10px; border:1px solid var(--line); border-radius:999px; color:var(--muted); background:#FCFAF5;}

  /* ---- top nav ---- */
  .topnav{position:sticky; top:0; z-index:50; background:rgba(248,244,234,.95); backdrop-filter:blur(8px); border-bottom:1px solid var(--line);}
  .topnav-inner{display:flex; align-items:center; justify-content:space-between; padding:14px 32px; max-width:1120px; margin:0 auto;}
  .logo{display:flex; align-items:center; gap:9px; font-family:'Noto Serif Thai',serif; font-weight:700; font-size:19px; color:var(--forest); cursor:pointer;}
  .logo .mark{width:12px; height:12px; background:var(--gold); border-radius:50%; box-shadow:0 0 0 3px rgba(192,146,58,.25);}
  .nav-links{display:flex; align-items:center; gap:6px;}
  .nav-links .nlink{padding:9px 16px; border-radius:999px; font-size:14px; font-weight:500; color:var(--muted); background:transparent; border:none; transition:all .15s;}
  .nav-links .nlink:hover{color:var(--forest); background:rgba(31,51,39,.05);}
  .nav-links .nlink.active{background:var(--forest); color:#fff;}

  /* ================= DISCOVER ================= */
  .hero{
    position:relative; overflow:hidden; background:var(--forest); color:#F3EFE3; padding:70px 0 130px;
  }
  .hero-pattern{position:absolute; inset:0; opacity:.16; pointer-events:none;}
  .hero-inner{position:relative; max-width:1120px; margin:0 auto; padding:0 32px;}
  .hero h1{font-size:40px; line-height:1.35; max-width:11em; margin:0 0 14px; font-weight:600;}
  .hero p{font-size:15.5px; color:#CBD3C4; max-width:32em; margin:0;}

  .search-card{
    position:relative; z-index:2; background:var(--panel); margin:-72px 32px 0; max-width:1056px; margin-left:auto; margin-right:auto;
    border-radius:14px; box-shadow:0 18px 40px rgba(20,30,20,.16);
    padding:22px 24px; display:grid; grid-template-columns:1.3fr 1fr 1fr .8fr auto; gap:14px; align-items:end;
  }
  .search-field label{display:block; font-size:11.5px; color:var(--muted); margin-bottom:6px; font-weight:600;}
  .search-field select, .search-field input{
    width:100%; height:44px; padding:0 14px; border:1px solid var(--line); border-radius:10px;
    font-family:inherit; font-size:14px; background:#FCFAF5; color:var(--text);
    transition:all .2s ease;
  }
  .search-field select:hover, .search-field input:hover{
    border-color:rgba(31,51,39,.35); background:#FFFFFF;
  }
  .search-field select:focus, .search-field input:focus{
    outline:none; border-color:var(--forest); background:#FFFFFF;
    box-shadow:0 0 0 3px rgba(31,51,39,.1);
  }

  /* Universal select styling (softened fallback for native selects) */
  select{
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23736C5A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 14px center;
    background-size:15px;
    padding-right:38px !important;
    cursor:pointer;
  }

  /* ================= CUSTOM DROPDOWN COMPONENT ================= */
  .custom-dropdown{
    position:relative; width:100%; user-select:none;
  }
  .dropdown-trigger{
    width:100%; height:44px; display:flex; align-items:center; justify-content:space-between;
    padding:0 14px; background:#FCFAF5; border:1px solid var(--line);
    border-radius:10px; font-family:inherit; font-size:14px; color:var(--text);
    cursor:pointer; transition:all .2s ease;
    box-shadow:0 1px 2px rgba(0,0,0,.02);
  }
  .dropdown-trigger:hover{
    border-color:rgba(31,51,39,.35); background:#FFFFFF;
    box-shadow:0 2px 8px rgba(0,0,0,.04);
  }
  .custom-dropdown.open .dropdown-trigger{
    border-color:var(--forest); background:#FFFFFF;
    box-shadow:0 0 0 3px rgba(31,51,39,.1);
  }
  .dropdown-selected-content{
    display:inline-flex; align-items:center; gap:10px; font-weight:500;
  }
  .dropdown-icon{
    display:inline-flex; align-items:center; justify-content:center;
    width:24px; height:24px; border-radius:6px; background:rgba(31,51,39,.06);
    color:var(--forest);
  }
  .dropdown-chevron{
    color:var(--muted); transition:transform .22s cubic-bezier(.4,0,.2,1);
    flex-shrink:0;
  }
  .custom-dropdown.open .dropdown-chevron{
    transform:rotate(180deg); color:var(--forest);
  }

  .dropdown-menu{
    position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:75;
    background:#FFFFFF; border:1px solid var(--line); border-radius:12px;
    box-shadow:0 14px 35px rgba(20,30,20,.14), 0 3px 8px rgba(0,0,0,.04);
    padding:6px; display:none; flex-direction:column; gap:3px;
    animation:dropdownIn .18s cubic-bezier(0.16, 1, 0.3, 1);
  }
  @keyframes dropdownIn{
    from{opacity:0; transform:translateY(-6px) scale(.98);}
    to{opacity:1; transform:translateY(0) scale(1);}
  }
  .custom-dropdown.open .dropdown-menu{
    display:flex;
  }

  .dropdown-item{
    display:flex; align-items:center; justify-content:space-between;
    padding:9px 12px; border-radius:8px; font-size:13.5px; font-weight:500;
    color:var(--text); cursor:pointer; transition:all .15s ease;
  }
  .dropdown-item .item-left{
    display:inline-flex; align-items:center; gap:10px;
  }
  .dropdown-item .item-icon{
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; border-radius:6px; background:#F5F1E8;
    color:var(--forest); transition:all .15s ease;
  }
  .dropdown-item .item-badge{
    font-size:11.5px; color:var(--muted); font-weight:normal; margin-left:6px;
  }
  .dropdown-item .item-check{
    opacity:0; color:currentColor; transition:opacity .15s ease;
  }
  .dropdown-item:hover{
    background:#F5F1E8; color:var(--forest);
  }
  .dropdown-item:hover .item-icon{
    background:var(--forest); color:#FFFFFF;
  }
  .dropdown-item.active{
    background:var(--forest); color:#FFFFFF; font-weight:600;
  }
  .dropdown-item.active .item-icon{
    background:rgba(255,255,255,.2); color:#FFFFFF;
  }
  .dropdown-item.active .item-badge{
    color:rgba(255,255,255,.75);
  }
  .dropdown-item.active .item-check{
    opacity:1;
  }

  /* ================= CUSTOM PICKERS (DATE & TIME) ================= */
  .datepicker-menu{
    position:absolute; top:calc(100% + 6px); left:0; z-index:75;
    background:#FFFFFF; border:1px solid var(--line); border-radius:14px;
    box-shadow:0 16px 40px rgba(20,30,20,.16), 0 3px 8px rgba(0,0,0,.04);
    padding:16px; width:300px; display:none; flex-direction:column;
    animation:dropdownIn .18s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .custom-dropdown.open .datepicker-menu{
    display:flex;
  }

  .dp-quick-row{
    display:flex; gap:6px; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid var(--line);
  }
  .dp-quick-btn{
    flex:1; padding:6px 8px; border-radius:6px; border:1px solid var(--line);
    background:#FCFAF5; font-size:11.5px; font-weight:600; color:var(--muted);
    cursor:pointer; transition:all .15s ease; text-align:center;
  }
  .dp-quick-btn:hover{
    background:var(--forest); color:#fff; border-color:var(--forest);
  }

  .dp-header{
    display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;
  }
  .dp-header .dp-month-year{
    font-size:14px; font-weight:600; color:var(--text); font-family:'Noto Serif Thai',serif;
  }
  .dp-nav-btn{
    width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:6px; border:1px solid var(--line); background:#FCFAF5; color:var(--text);
    cursor:pointer; transition:all .15s ease; padding:0; font-size:15px; font-weight:bold;
  }
  .dp-nav-btn:hover{
    background:var(--forest); color:#fff; border-color:var(--forest);
  }

  .dp-weekdays{
    display:grid; grid-template-columns:repeat(7, 1fr); text-align:center;
    font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:6px;
  }
  .dp-days-grid{
    display:grid; grid-template-columns:repeat(7, 1fr); gap:3px;
  }
  .dp-day-cell{
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:12.5px; font-weight:500; border-radius:8px; cursor:pointer;
    color:var(--text); transition:all .15s ease; border:none; background:none; padding:0;
  }
  .dp-day-cell:hover:not(.disabled){
    background:#F4EFE6; color:var(--forest); font-weight:600;
  }
  .dp-day-cell.today{
    position:relative; font-weight:700; color:var(--forest);
  }
  .dp-day-cell.today::after{
    content:''; position:absolute; bottom:3px; width:4px; height:4px; border-radius:50%; background:var(--gold);
  }
  .dp-day-cell.selected{
    background:var(--forest) !important; color:#FFFFFF !important; font-weight:600;
  }
  .dp-day-cell.selected::after{
    background:#FFFFFF;
  }
  .dp-day-cell.disabled{
    color:#D0C8B8; cursor:not-allowed; opacity:.5;
  }
  .dp-day-cell.other-month{
    color:#C8C2B4; opacity:.6;
  }

  /* Time Picker Menu */
  .timepicker-menu{
    position:absolute; top:calc(100% + 6px); left:0; z-index:75;
    background:#FFFFFF; border:1px solid var(--line); border-radius:14px;
    box-shadow:0 16px 40px rgba(20,30,20,.16), 0 3px 8px rgba(0,0,0,.04);
    padding:14px; width:320px; display:none; flex-direction:column; gap:10px;
    animation:dropdownIn .18s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .custom-dropdown.open .timepicker-menu{
    display:flex;
  }

  .tp-tabs{
    display:flex; gap:4px; background:#F8F4EA; padding:3px; border-radius:10px; border:1px solid var(--line);
  }
  .tp-tab{
    flex:1; padding:6px 2px; border:none; background:none; border-radius:7px;
    font-size:11.5px; font-weight:600; color:var(--muted); cursor:pointer;
    transition:all .15s ease; text-align:center;
  }
  .tp-tab:hover{
    color:var(--forest);
  }
  .tp-tab.active{
    background:#FFFFFF; color:var(--forest); box-shadow:0 2px 5px rgba(0,0,0,.08);
  }

  .tp-grid{
    display:grid; grid-template-columns:repeat(4, 1fr); gap:6px; max-height:190px; overflow-y:auto; padding:2px;
  }
  .tp-grid::-webkit-scrollbar{
    width:4px;
  }
  .tp-grid::-webkit-scrollbar-thumb{
    background:#D8D2C2; border-radius:4px;
  }
  .tp-slot{
    padding:7px 3px; text-align:center; font-size:12.5px; font-weight:500;
    border-radius:8px; border:1px solid var(--line); background:#FCFAF5;
    color:var(--text); cursor:pointer; transition:all .15s ease;
  }
  .tp-slot:hover{
    background:#F4EFE6; color:var(--forest); border-color:var(--forest);
  }
  .tp-slot.selected{
    background:var(--forest); color:#FFFFFF; border-color:var(--forest); font-weight:600;
  }

  /* Custom Time Input Row */
  .tp-custom-bar{
    border-top:1px solid var(--line); padding-top:10px; margin-top:2px;
  }
  .tp-custom-label{
    font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between; align-items:center;
  }
  .tp-custom-inputs{
    display:flex; align-items:center; gap:6px;
  }
  .tp-custom-select{
    flex:1; height:36px; padding:0 6px; border:1px solid var(--line); border-radius:8px;
    background:#FCFAF5; font-size:13px; font-weight:600; color:var(--text); font-family:inherit;
  }
  .tp-custom-select:focus{
    outline:none; border-color:var(--forest); background:#fff;
  }

  @media(max-width:600px){
    .datepicker-menu, .timepicker-menu{
      width:100% !important; min-width:280px;
    }
  }

  .section{padding:56px 0;}
  .section-head{display:flex; align-items:baseline; justify-content:space-between; margin-bottom:22px;}
  .section-head h2{font-size:23px; margin:0; font-weight:600;}
  .section-head .meta{font-size:13px; color:var(--muted);}

  .chip-row{display:flex; gap:10px; flex-wrap:wrap; margin-bottom:34px;}
  .chip{padding:9px 18px; border-radius:999px; border:1px solid var(--line); background:var(--panel); font-size:13.5px; font-weight:500; color:var(--muted); cursor:pointer; transition:all .15s;}
  .chip:hover{border-color:var(--forest); color:var(--forest);}
  .chip.active{background:var(--forest); color:#fff; border-color:var(--forest);}

  .venue-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px;}
  .venue-card{background:var(--panel); border:1px solid var(--line); border-radius:14px; overflow:hidden; cursor:pointer; transition:transform .2s ease, box-shadow .2s ease;}
  .venue-card:hover{transform:translateY(-4px); box-shadow:0 12px 28px rgba(20,30,20,.1);}
  .venue-thumb{height:165px; position:relative; display:flex; align-items:flex-end; padding:12px; background-size:cover; background-position:center; background-repeat:no-repeat; overflow:hidden;}
  .venue-thumb::before{content:''; position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.68) 0%, rgba(0,0,0,.18) 55%, rgba(0,0,0,.02) 100%); pointer-events:none;}
  .venue-thumb .cat-badge{position:relative; z-index:2; background:rgba(255,255,255,.94); color:var(--forest); font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; box-shadow:0 2px 6px rgba(0,0,0,.12);}
  .venue-body{padding:16px 18px 18px;}
  .venue-body .name{font-family:'Noto Serif Thai',serif; font-size:17px; font-weight:600; margin-bottom:4px; color:var(--text);}
  .venue-body .meta-line{font-size:12.5px; color:var(--muted); margin-bottom:10px;}
  .venue-body .foot{display:flex; align-items:center; justify-content:space-between; margin-top:10px;}
  .stars{font-size:12.5px; color:var(--gold-dark); font-weight:600;}

  /* ================= VENUE DETAIL ================= */
  .venue-header{padding:36px 0 20px; border-bottom:1px solid var(--line);}
  .back-link{display:inline-flex; align-items:center; gap:6px; font-size:13.5px; color:var(--muted); margin-bottom:14px; background:none; border:none; padding:0;}
  .back-link:hover{color:var(--forest);}
  .venue-title-row{display:flex; justify-content:space-between; align-items:flex-start; gap:20px;}
  .venue-title-row h1{font-size:30px; margin:0 0 8px;}
  .venue-title-row .meta{font-size:13.5px; color:var(--muted); display:flex; gap:16px; flex-wrap:wrap;}
  .type-switch{display:flex; gap:6px; background:var(--ivory); border:1px solid var(--line); border-radius:999px; padding:3px;}
  .type-switch button{border:none; background:none; padding:7px 14px; border-radius:999px; font-size:12.5px; font-weight:600; color:var(--muted); transition:all .15s;}
  .type-switch button.active{background:var(--forest); color:#fff;}

  .photo-strip{display:grid; grid-template-columns:2fr 1fr 1fr; gap:10px; margin:24px 0;}
  .photo-strip div{height:180px; border-radius:10px; background-size:cover; background-position:center; background-repeat:no-repeat;}
  .photo-strip div:first-child{height:100%;}

  .venue-layout{display:grid; grid-template-columns:1fr 340px; gap:28px; padding-bottom:70px;}
  .venue-main .desc{font-size:14.5px; line-height:1.8; color:#3A4133; margin-bottom:22px;}
  .amenities{display:flex; gap:8px; flex-wrap:wrap; margin-bottom:30px;}

  .floorplan-panel{background:var(--panel); border:1px solid var(--line); border-radius:14px; padding:22px;}
  .floorplan-panel h3{margin:0 0 4px; font-size:17px;}
  .floorplan-panel .hint{font-size:12.5px; color:var(--muted); margin-bottom:16px;}
  .legend{display:flex; gap:16px; font-size:12px; color:var(--muted); margin-bottom:18px;}
  .legend span{display:inline-flex; align-items:center; gap:6px;}
  .legend i{width:10px; height:10px; border-radius:3px; display:inline-block;}
  .fp-grid{display:grid; grid-template-columns:repeat(6,1fr); gap:12px;}
  .fp-item{
    aspect-ratio:1; border-radius:10px; display:flex; flex-direction:column; align-items:center; justify-content:center;
    font-size:12px; font-weight:700; color:#fff; background:#3F7A52; cursor:pointer; border:2px solid transparent; gap:2px;
    transition:all .15s ease;
  }
  .fp-item:hover:not(.taken){transform:scale(1.04); filter:brightness(1.08);}
  .fp-item small{font-weight:500; font-size:9.5px; opacity:.9;}
  .fp-item.taken{background:#D8D2C2; color:#8C8570; cursor:not-allowed;}
  .fp-item.selected{background:var(--gold); border-color:var(--gold-dark); transform:scale(1.06); box-shadow:0 6px 14px rgba(192,146,58,.3);}
  .fp-item.round{border-radius:50%;}

  .booking-panel{position:sticky; top:88px; background:var(--panel); border:1px solid var(--line); border-radius:14px; padding:22px; align-self:start; box-shadow:0 10px 30px rgba(0,0,0,.04);}
  .booking-panel h3{margin:0 0 16px; font-size:17px;}
  .bf{margin-bottom:14px;}
  .bf label{display:block; font-size:12px; color:var(--muted); margin-bottom:6px; font-weight:600;}
  .bf input, .bf select{width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:8px; font-family:inherit; font-size:14px; background:#FCFAF5;}
  .bf input:focus, .bf select:focus{outline:none; border-color:var(--forest);}
  .slot-row{display:flex; gap:8px; flex-wrap:wrap;}
  .slot{padding:8px 12px; border:1px solid var(--line); border-radius:8px; font-size:12.5px; background:#FCFAF5; cursor:pointer; transition:all .15s;}
  .slot:hover:not(.full){border-color:var(--forest);}
  .slot.active{background:var(--forest); color:#fff; border-color:var(--forest);}
  .slot.full{opacity:.4; text-decoration:line-through; cursor:not-allowed;}
  .stepper{display:flex; align-items:center; gap:12px;}
  .stepper button{width:32px; height:32px; border-radius:8px; border:1px solid var(--line); background:#FCFAF5; font-size:16px; display:inline-flex; align-items:center; justify-content:center;}
  .stepper button:hover{background:var(--line);}
  .booking-summary{border-top:1px dashed var(--line); margin-top:16px; padding-top:14px; font-size:13px;}
  .booking-summary .row{display:flex; justify-content:space-between; margin-bottom:8px; color:var(--muted);}
  .booking-summary .row b{color:var(--text); font-weight:600;}

  /* ================= CONFIRM (TICKET) ================= */
  .confirm-wrap{display:flex; justify-content:center; padding:70px 20px 90px;}
  .ticket{
    width:420px; background:var(--panel); border-radius:16px; box-shadow:0 20px 50px rgba(20,30,20,.15); overflow:hidden;
    animation:ticketPop .3s ease-out;
  }
  @keyframes ticketPop{from{opacity:0; transform:scale(.95);} to{opacity:1; transform:scale(1);}}
  .ticket-top{background:var(--forest); color:#fff; padding:30px 28px 24px; text-align:center;}
  .ticket-top .check{width:52px; height:52px; border-radius:50%; background:var(--gold); display:flex; align-items:center; justify-content:center; margin:0 auto 14px;}
  .ticket-top h2{margin:0 0 6px; font-size:20px;}
  .ticket-top p{margin:0; font-size:13px; color:#CBD3C4;}
  .ticket-notch{height:20px; background:var(--forest); position:relative;}
  .ticket-notch::before, .ticket-notch::after{content:''; position:absolute; top:-10px; width:20px; height:20px; background:var(--ivory); border-radius:50%;}
  .ticket-notch::before{left:-10px;}
  .ticket-notch::after{right:-10px;}
  .ticket-body{padding:26px 28px;}
  .ticket-body .code{text-align:center; font-size:20px; letter-spacing:.08em; margin-bottom:20px; font-weight:600; color:var(--forest);}
  .ticket-body .row{display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px dashed var(--line); font-size:13.5px;}
  .ticket-body .row:last-child{border-bottom:none;}
  .ticket-body .row span:first-child{color:var(--muted);}
  .ticket-body .row span:last-child{font-weight:600;}
  .ticket-actions{padding:0 28px 28px; display:flex; gap:10px;}
  .ticket-actions .btn{flex:1;}

  /* ================= MY BOOKINGS ================= */
  .tabs{display:flex; gap:8px; margin-bottom:24px;}
  .tabs button{padding:9px 18px; border-radius:999px; border:1px solid var(--line); background:var(--panel); font-size:13.5px; font-weight:600; color:var(--muted); transition:all .15s;}
  .tabs button:hover{border-color:var(--forest); color:var(--forest);}
  .tabs button.active{background:var(--forest); color:#fff; border-color:var(--forest);}
  .booking-list{display:flex; flex-direction:column; gap:14px;}
  .bcard{display:flex; align-items:center; gap:18px; background:var(--panel); border:1px solid var(--line); border-radius:12px; padding:16px 20px; transition:transform .15s ease;}
  .bcard:hover{transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.04);}
  .bcard .thumb{width:56px; height:56px; border-radius:10px; flex-shrink:0;}
  .bcard .info{flex:1;}
  .bcard .info .name{font-weight:600; font-size:14.5px; margin-bottom:3px;}
  .bcard .info .meta{font-size:12.5px; color:var(--muted);}
  .bcard .code{font-size:11.5px; color:var(--muted); margin-top:2px;}

  /* ================= OWNER DASHBOARD ================= */
  .owner-head{display:flex; justify-content:space-between; align-items:center; margin-bottom:22px;}
  .owner-stats{display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:26px;}
  .ostat{background:var(--panel); border:1px solid var(--line); border-radius:12px; padding:16px 18px; border-left:3px solid var(--gold);}
  .ostat .label{font-size:12.5px; color:var(--muted);}
  .ostat .value{font-family:'JetBrains Mono',monospace; font-size:24px; margin-top:6px; font-weight:600;}
  .owner-grid{display:grid; grid-template-columns:1.5fr 1fr; gap:18px;}
  .opanel{background:var(--panel); border:1px solid var(--line); border-radius:12px; overflow:hidden;}
  .opanel-head{padding:14px 18px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;}
  .opanel-head h3{margin:0; font-size:15px;}
  table{width:100%; border-collapse:collapse;}
  th{text-align:left; font-size:11px; color:var(--muted); font-weight:600; padding:10px 18px; border-bottom:1px solid var(--line); background:#FBF8F1;}
  td{padding:11px 18px; border-bottom:1px solid var(--line); font-size:13px;}
  tr:last-child td{border-bottom:none;}
  .mini-fp{display:grid; grid-template-columns:repeat(6,1fr); gap:8px; padding:18px;}
  .mini-fp div{aspect-ratio:1; border-radius:6px; background:#3F7A52; display:flex; align-items:center; justify-content:center; font-size:10px; color:#fff; font-weight:600;}
  .mini-fp div.taken{background:#D8D2C2; color:#8C8570;}
  .mini-fp div.pending{background:var(--gold);}

  /* Toast Notification */
  .toast{
    position:fixed; bottom:24px; right:24px; z-index:100;
    background:var(--forest); color:#fff; padding:14px 20px; border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,.2); display:none; align-items:center; gap:10px;
    font-size:14px; animation:toastIn .3s ease;
  }
  @keyframes toastIn{from{transform:translateY(20px); opacity:0;} to{transform:translateY(0); opacity:1;}}

  /* Modal */
  .modal-overlay{
    position:fixed; inset:0; background:rgba(20,30,20,.6); backdrop-filter:blur(4px);
    z-index:90; display:none; align-items:center; justify-content:center; padding:20px;
  }
  .modal-overlay.active{display:flex;}
  .modal{
    background:var(--panel); border-radius:14px; width:100%; max-width:440px;
    box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; animation:ticketPop .25s ease-out;
  }
  .modal-header{padding:18px 22px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;}
  .modal-header h3{margin:0; font-size:17px;}
  .modal-close{background:none; border:none; font-size:20px; color:var(--muted); line-height:1;}
  .modal-body{padding:22px;}
  .modal-body label{display:block; font-size:12px; color:var(--muted); font-weight:600; margin-bottom:6px;}
  .modal-body input, .modal-body select{width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:8px; margin-bottom:14px; font-family:inherit;}
  .modal-body input:focus, .modal-body select:focus{outline:none; border-color:var(--forest);}

  /* Auth Tabs & Error Alert */
  .auth-tabs{display:flex; border-bottom:1px solid var(--line); margin:-6px -22px 18px; padding:0 22px; gap:8px;}
  .auth-tab{padding:10px 16px; background:none; border:none; border-bottom:2px solid transparent; font-size:14px; font-weight:600; color:var(--muted); cursor:pointer; transition:all .15s;}
  .auth-tab:hover{color:var(--forest);}
  .auth-tab.active{color:var(--forest); border-bottom-color:var(--forest);}
  .auth-error-box{background:var(--rust-bg); color:var(--rust); border-radius:8px; padding:10px 14px; font-size:12.5px; margin-bottom:14px; display:none; line-height:1.5;}
  .auth-error-box ul{margin:0; padding-left:18px;}

  @media(max-width:900px){
    .venue-grid{grid-template-columns:1fr 1fr;}
    .venue-layout{grid-template-columns:1fr;}
    .search-card{grid-template-columns:1fr 1fr;}
    .owner-stats{grid-template-columns:1fr 1fr;}
    .owner-grid{grid-template-columns:1fr;}
    .photo-strip{grid-template-columns:1fr;}
    .photo-strip div:first-child{height:180px;}
    .photo-strip div:not(:first-child){display:none;}
  }

  @media(max-width:600px){
    .venue-grid{grid-template-columns:1fr;}
    .search-card{grid-template-columns:1fr;}
    .owner-stats{grid-template-columns:1fr;}
    .topnav-inner{padding:12px 16px;}
    .wrap{padding:0 16px;}
    .hero h1{font-size:28px;}
    .fp-grid{grid-template-columns:repeat(4,1fr);}
  }
</style>
@stack('styles')
</head>
<body>

<nav class="topnav">
  <div class="topnav-inner">
    <div class="logo" onclick="showScreen('discover', document.querySelector('[data-screen=discover]'))">
      <span class="mark"></span>จองทันใจ
    </div>
    <div class="nav-links">
      <button class="nlink active" data-screen="discover" onclick="showScreen('discover',this)">ค้นหาที่จอง</button>
      <button class="nlink" data-screen="mybookings" onclick="showScreen('mybookings',this)">การจองของฉัน</button>
      <button class="nlink" data-screen="owner" onclick="showScreen('owner',this)">สำหรับเจ้าของร้าน</button>
    </div>
    <div id="topnav-auth-area">
      @auth
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:13.5px; font-weight:600; color:var(--forest); display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ Auth::user()->name }}
          </span>
          <button class="btn btn-outline btn-sm" onclick="submitLogout()">ออกจากระบบ</button>
        </div>
      @else
        <div style="display:flex; align-items:center; gap:8px;">
          <button class="btn btn-outline btn-sm" onclick="openAuthModal('login')">เข้าสู่ระบบ</button>
          <button class="btn btn-gold btn-sm" onclick="openAuthModal('register')">สมัครสมาชิก</button>
        </div>
      @endauth
    </div>
  </div>
</nav>

<main style="flex:1;">
  @yield('content')
</main>

<div id="toast" class="toast">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C0923A" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toast-message">แจ้งเตือน</span>
</div>

<!-- Modal: เข้าสู่ระบบ / สมัครสมาชิก -->
<div id="modal-auth" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h3 id="auth-modal-title">เข้าสู่ระบบ</h3>
      <button class="modal-close" onclick="closeModal('modal-auth')">×</button>
    </div>
    <div class="modal-body">
      <!-- Tabs Switcher -->
      <div class="auth-tabs">
        <button type="button" class="auth-tab active" id="tab-btn-login" onclick="switchAuthTab('login')">เข้าสู่ระบบ</button>
        <button type="button" class="auth-tab" id="tab-btn-register" onclick="switchAuthTab('register')">สมัครสมาชิก</button>
      </div>

      <!-- Error Box -->
      <div id="auth-error-box" class="auth-error-box"></div>

      <!-- Login Form -->
      <form id="form-login" onsubmit="submitLogin(event)">
        <p style="font-size:13px; color:var(--muted); margin-top:0; margin-bottom:14px;">เข้าสู่ระบบเพื่อจัดการการจองและบันทึกประวัติของคุณ</p>
        <label>เบอร์โทรศัพท์ หรือ อีเมล <span style="color:var(--rust);">*</span></label>
        <input type="text" id="login-identifier" name="login" placeholder="เช่น somchai@example.com หรือ 081-234-5678" required autocomplete="username">
        
        <label>รหัสผ่าน <span style="color:var(--rust);">*</span></label>
        <input type="password" id="login-password" name="password" placeholder="กรอกรหัสผ่านของคุณ" required autocomplete="current-password">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; font-size:12.5px;">
          <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; margin:0; font-weight:normal; color:var(--text);">
            <input type="checkbox" id="login-remember" style="width:auto; margin:0;"> จดจำการเข้าสู่ระบบ
          </label>
          <span style="color:var(--muted); font-size:12px;">รหัสเริ่มต้น: <code>password</code></span>
        </div>

        <button type="submit" id="btn-login-submit" class="btn btn-gold" style="width:100%;">
          เข้าสู่ระบบ
        </button>

        <div style="text-align:center; margin-top:16px; font-size:13px; color:var(--muted);">
          ยังไม่มีบัญชีใช่หรือไม่? <a href="javascript:void(0)" onclick="switchAuthTab('register')" style="color:var(--forest); font-weight:600; text-decoration:underline;">สมัครสมาชิกที่นี่</a>
        </div>
      </form>

      <!-- Register Form -->
      <form id="form-register" onsubmit="submitRegister(event)" style="display:none;">
        <p style="font-size:13px; color:var(--muted); margin-top:0; margin-bottom:14px;">สร้างบัญชีผู้ใช้ใหม่เพื่อเริ่มจองสถานที่ได้ทันที</p>
        
        <label>ชื่อ - นามสกุล <span style="color:var(--rust);">*</span></label>
        <input type="text" id="reg-name" name="name" placeholder="เช่น สมศักดิ์ มีสุข" required autocomplete="name">

        <label>เบอร์โทรศัพท์</label>
        <input type="tel" id="reg-phone" name="phone" placeholder="เช่น 089-123-4567" autocomplete="tel">

        <label>อีเมล <span style="color:var(--rust);">*</span></label>
        <input type="email" id="reg-email" name="email" placeholder="เช่น somsak@example.com" required autocomplete="email">

        <label>รหัสผ่าน (อย่างน้อย 6 ตัวอักษร) <span style="color:var(--rust);">*</span></label>
        <input type="password" id="reg-password" name="password" placeholder="ตั้งรหัสผ่านของคุณ" required minlength="6" autocomplete="new-password">

        <label>ยืนยันรหัสผ่าน <span style="color:var(--rust);">*</span></label>
        <input type="password" id="reg-password-confirmation" name="password_confirmation" placeholder="กรอกรหัสผ่านอีกครั้ง" required minlength="6" autocomplete="new-password">

        <button type="submit" id="btn-register-submit" class="btn btn-gold" style="width:100%; margin-top:4px;">
          ยืนยันการสมัครสมาชิก
        </button>

        <div style="text-align:center; margin-top:16px; font-size:13px; color:var(--muted);">
          มีบัญชีอยู่แล้ว? <a href="javascript:void(0)" onclick="switchAuthTab('login')" style="color:var(--forest); font-weight:600; text-decoration:underline;">เข้าสู่ระบบที่นี่</a>
        </div>
      </form>
    </div>
  </div>
</div>

<footer style="background:var(--forest); color:#CBD3C4; padding:32px 0 28px; margin-top:40px; border-top:1px solid rgba(255,255,255,.08);">
  <div class="wrap" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; font-size:13px;">
    <div style="display:flex; align-items:center; gap:8px; color:#fff; font-weight:600;">
      <span style="width:8px; height:8px; border-radius:50%; background:var(--gold); display:inline-block;"></span> จองทันใจ (Instant Booking) © {{ date('Y') }}
    </div>
    <div style="display:flex; gap:18px; color:#A8B39F;">
      <span>ร้านอาหาร</span>
      <span>สนามกีฬา</span>
      <span>คาเฟ่</span>
      <span>ห้องประชุม</span>
    </div>
  </div>
</footer>

<script>
function showToast(msg) {
  const toast = document.getElementById('toast');
  document.getElementById('toast-message').textContent = msg;
  toast.style.display = 'inline-flex';
  setTimeout(() => { toast.style.display = 'none'; }, 3500);
}

function openAuthModal(tab = 'login') {
  switchAuthTab(tab);
  document.getElementById('auth-error-box').style.display = 'none';
  document.getElementById('auth-error-box').innerHTML = '';
  document.getElementById('modal-auth').classList.add('active');
}

function openLoginModal(tab = 'login') {
  openAuthModal(tab);
}

function switchAuthTab(tab) {
  const isLogin = tab === 'login';
  document.getElementById('tab-btn-login').classList.toggle('active', isLogin);
  document.getElementById('tab-btn-register').classList.toggle('active', !isLogin);
  document.getElementById('form-login').style.display = isLogin ? 'block' : 'none';
  document.getElementById('form-register').style.display = isLogin ? 'none' : 'block';
  document.getElementById('auth-modal-title').textContent = isLogin ? 'เข้าสู่ระบบ' : 'สมัครสมาชิก';
  document.getElementById('auth-error-box').style.display = 'none';
  document.getElementById('auth-error-box').innerHTML = '';
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('active');
}

function showAuthErrors(errors) {
  const box = document.getElementById('auth-error-box');
  if (!box) return;
  let html = '<ul style="margin:0; padding-left:18px;">';
  if (typeof errors === 'string') {
    html += `<li>${errors}</li>`;
  } else if (Array.isArray(errors)) {
    errors.forEach(e => { html += `<li>${e}</li>`; });
  } else if (typeof errors === 'object') {
    Object.values(errors).forEach(errList => {
      if (Array.isArray(errList)) {
        errList.forEach(e => { html += `<li>${e}</li>`; });
      } else {
        html += `<li>${errList}</li>`;
      }
    });
  }
  html += '</ul>';
  box.innerHTML = html;
  box.style.display = 'block';
}

async function submitLogin(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-login-submit');
  const loginVal = document.getElementById('login-identifier').value.trim();
  const passwordVal = document.getElementById('login-password').value;
  const rememberVal = document.getElementById('login-remember').checked;
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  btn.disabled = true;
  btn.textContent = 'กำลังเข้าสู่ระบบ...';
  document.getElementById('auth-error-box').style.display = 'none';

  try {
    const res = await fetch('{{ route("login") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        login: loginVal,
        password: passwordVal,
        remember: rememberVal
      })
    });

    const data = await res.json();
    if (res.ok && data.success) {
      closeModal('modal-auth');
      showToast('เข้าสู่ระบบสำเร็จ: คุณ ' + data.user.name);
      setTimeout(() => { window.location.reload(); }, 600);
    } else {
      showAuthErrors(data.errors || data.message || 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
    }
  } catch (err) {
    console.error(err);
    showAuthErrors('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
  } finally {
    btn.disabled = false;
    btn.textContent = 'เข้าสู่ระบบ';
  }
}

async function submitRegister(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-register-submit');
  const nameVal = document.getElementById('reg-name').value.trim();
  const phoneVal = document.getElementById('reg-phone').value.trim();
  const emailVal = document.getElementById('reg-email').value.trim();
  const passwordVal = document.getElementById('reg-password').value;
  const passwordConfirmVal = document.getElementById('reg-password-confirmation').value;
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  btn.disabled = true;
  btn.textContent = 'กำลังลงทะเบียน...';
  document.getElementById('auth-error-box').style.display = 'none';

  try {
    const res = await fetch('{{ route("register") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        name: nameVal,
        phone: phoneVal,
        email: emailVal,
        password: passwordVal,
        password_confirmation: passwordConfirmVal
      })
    });

    const data = await res.json();
    if (res.ok && data.success) {
      closeModal('modal-auth');
      showToast('สมัครสมาชิกและเข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ ' + data.user.name);
      setTimeout(() => { window.location.reload(); }, 600);
    } else {
      showAuthErrors(data.errors || data.message || 'เกิดข้อผิดพลาดในการสมัครสมาชิก');
    }
  } catch (err) {
    console.error(err);
    showAuthErrors('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
  } finally {
    btn.disabled = false;
    btn.textContent = 'ยืนยันการสมัครสมาชิก';
  }
}

async function submitLogout() {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  try {
    const res = await fetch('{{ route("logout") }}', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      }
    });
    if (res.ok) {
      showToast('ออกจากระบบเรียบร้อยแล้ว');
      setTimeout(() => { window.location.reload(); }, 500);
    }
  } catch (err) {
    console.error(err);
    window.location.reload();
  }
}

/* Custom Dropdown Controller */
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  if (!dropdown) return;
  const isOpen = dropdown.classList.contains('open');
  closeAllDropdowns();
  if (!isOpen) {
    dropdown.classList.add('open');
    dropdown.querySelector('.dropdown-trigger')?.setAttribute('aria-expanded', 'true');
  }
}

function closeAllDropdowns() {
  document.querySelectorAll('.custom-dropdown.open').forEach(el => {
    el.classList.remove('open');
    el.querySelector('.dropdown-trigger')?.setAttribute('aria-expanded', 'false');
  });
}

document.addEventListener('click', (e) => {
  if (!e.target.closest('.custom-dropdown')) {
    closeAllDropdowns();
  }
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeAllDropdowns();
  }
});
</script>
@stack('scripts')
</body>
</html>
