<?php


  function ARCRangerPlayAbility($cardID, $from, $resourcesPaid, $target = "-", $additionalCosts = "")
  {
    global $currentPlayer;
    switch($cardID)
    {
      case "ARC038": case "ARC039":
        if(ArsenalEmpty($currentPlayer)) return "There is no card in your arsenal.";
        AddDecisionQueue("FINDINDICES", $currentPlayer, "ARSENAL");
        AddDecisionQueue("CHOOSEARSENAL", $currentPlayer, "<-", 1);
        AddDecisionQueue("REMOVEARSENAL", $currentPlayer, "-", 1);
        AddDecisionQueue("ADDBOTDECK", $currentPlayer, "-", 1);
        AddDecisionQueue("PARAMDELIMTOARRAY", $currentPlayer, "0", 1);
        AddDecisionQueue("MULTIREMOVEDECK", $currentPlayer, "-", 1);
        AddDecisionQueue("NULLPASS", $currentPlayer, "-", 1);
        AddDecisionQueue("ADDARSENALFACEUP", $currentPlayer, "DECK", 1);
        AddDecisionQueue("ALLCARDSUBTYPEORPASS", $currentPlayer, "Arrow", 1);
        AddDecisionQueue("AZALEA", $currentPlayer, $cardID, 1);
        return "";
      case "ARC040":
        if(!ArsenalEmpty($currentPlayer)) return "There is already a card in your arsenal, so you cannot put an arrow in your arsenal.";
        AddDecisionQueue("FINDINDICES", $currentPlayer, "MYHANDARROW");
        AddDecisionQueue("MAYCHOOSEHAND", $currentPlayer, "<-", 1);
        AddDecisionQueue("REMOVEMYHAND", $currentPlayer, "-", 1);
        AddDecisionQueue("ADDARSENALFACEUP", $currentPlayer, "HAND", 1);
        AddDecisionQueue("DRAW", $currentPlayer, "-", 1);
        return "";
      case "ARC041":
          Opt($cardID, 1);
        return "";
      case "ARC042":
        if(!ArsenalEmpty($currentPlayer)) return "There is already a card in your arsenal, so you cannot put an arrow in your arsenal.";
        AddDecisionQueue("FINDINDICES", $currentPlayer, "MYHANDARROW");
        AddDecisionQueue("MAYCHOOSEHAND", $currentPlayer, "<-", 1);
        AddDecisionQueue("REMOVEMYHAND", $currentPlayer, "-", 1);
        AddDecisionQueue("ADDARSENALFACEUP", $currentPlayer, "HAND", 1);
        AddDecisionQueue("BULLEYESBRACERS", $currentPlayer, $cardID, 1);
        return "";
      case "ARC044":
        Draw($currentPlayer);
        Draw($currentPlayer);
        Draw($currentPlayer);
        AddCurrentTurnEffect($cardID, $currentPlayer);
        return "";
      case "ARC046":
        AddDecisionQueue("FINDINDICES", $currentPlayer, "MYDECKARROW");
        AddDecisionQueue("MAYCHOOSEDECK", $currentPlayer, "<-", 1);
        AddDecisionQueue("SHUFFLEDECK", $currentPlayer, "-");
        AddDecisionQueue("MULTIADDTOPDECK", $currentPlayer, "-", 1);
        AddDecisionQueue("REVEALCARDS", $currentPlayer, "-", 1);
        Reload();
        return "";
      case "ARC047":
        AddCurrentTurnEffect($cardID, $currentPlayer);
        Reload();
        return "";
      case "ARC048": case "ARC049": case "ARC050":
        Reload();
        return "";
      case "ARC051": case "ARC052": case "ARC053":
        if(!ArsenalEmpty($currentPlayer)) return "Did nothing because your arsenal is not empty.";
        if($cardID == "ARC051") $count = 4;
        else if($cardID == "ARC052") $count = 3;
        else $count = 2;
        AddDecisionQueue("FINDINDICES", $currentPlayer, "DECKTOPXREMOVE," . $count);
        AddDecisionQueue("SETDQVAR", $currentPlayer, "0", 1);
        AddDecisionQueue("FILTER", $currentPlayer, "LastResult-include-subtype-Arrow", 1);
        AddDecisionQueue("CHOOSECARD", $currentPlayer, "<-", 1);
        AddDecisionQueue("ADDARSENALFACEUP", $currentPlayer, "DECK", 1);
        AddDecisionQueue("OP", $currentPlayer, "REMOVECARD");
        AddDecisionQueue("CHOOSEBOTTOM", $currentPlayer, "<-");
        return "";
      case "ARC054": case "ARC055": case "ARC056":
        AddCurrentTurnEffect($cardID, $currentPlayer);
        Reload();
        return "";
      default: return "";
    }
  }

  function ARCRangerHitEffect($cardID)
  {
    global $defPlayer, $combatChainState, $CCS_GoesWhereAfterLinkResolves;
    switch($cardID)
    {
      case "ARC043":
        if(IsHeroAttackTarget())
        {
          AddNextTurnEffect($cardID, $defPlayer);
        }
        break;
      case "ARC045":
        $combatChainState[$CCS_GoesWhereAfterLinkResolves] = "HAND";
        break;
      case "ARC060": case "ARC061": case "ARC062":
        if(IsHeroAttackTarget())
        {
          AddNextTurnEffect($cardID, $defPlayer);
        }
        break;
      case "ARC066": case "ARC067": case "ARC068":
        $combatChainState[$CCS_GoesWhereAfterLinkResolves] = "BOTDECK";
        break;
      case "ARC069": case "ARC070": case "ARC071":
        if(IsHeroAttackTarget())
        {
          PlayerLoseHealth($defPlayer, 1);
        }
        break;
      default: break;
    }
    return "";
  }

  function Reload($player=0)
  {
    global $currentPlayer;
    if($player == 0) $player = $currentPlayer;
    if(!ArsenalEmpty($player))
    {
      WriteLog("Your arsenal is not empty, so you cannot Reload.");
      return;
    }
    AddDecisionQueue("FINDINDICES", $player, "HAND");
    AddDecisionQueue("SETDQCONTEXT", $player, "Choose a card to Reload");
    AddDecisionQueue("MAYCHOOSEHAND", $player, "<-", 1);
    AddDecisionQueue("MULTIREMOVEHAND", $player, "-", 1);
    AddDecisionQueue("ADDARSENALFACEDOWN", $player, "HAND", 1);
  }

  function SuperReload($player=0)
  {
    global $currentPlayer;
    if($player == 0) $player = $currentPlayer;
    if(ArsenalFull($player))
    {
      WriteLog("Your arsenal is full, so you do not arsenal a card.");
      return;
    }
    AddDecisionQueue("FINDINDICES", $player, "HAND");
    AddDecisionQueue("SETDQCONTEXT", $player, "Choose a card to put in your arsenal");
    AddDecisionQueue("MAYCHOOSEHAND", $player, "<-", 1);
    AddDecisionQueue("MULTIREMOVEHAND", $player, "-", 1);
    AddDecisionQueue("ADDARSENALFACEDOWN", $player, "HAND", 1);
  }

  function ReloadArrow($player = 0, $counters="0")
  {
    global $currentPlayer;
    if ($player == 0) $player = $currentPlayer;
    if (ArsenalFull($player)) {
      WriteLog("Your arsenal is full, so you cannot put an arrow in your arsenal.");
      return;
    }
    AddDecisionQueue("FINDINDICES", $currentPlayer, "MYHANDARROW");
    AddDecisionQueue("MAYCHOOSEHAND", $currentPlayer, "<-", 1);
    AddDecisionQueue("REMOVEMYHAND", $currentPlayer, "-", 1);
    AddDecisionQueue("ADDARSENALFACEUP", $currentPlayer, "HAND-" . $counters, 1);
  }
?>
