<?php
/**
 * Store call recording data so it can be easily sorted
 * later and the view can be abstracted
 *
 * @author chris
 */
class RecordingList
{
    const INBOUND = 'inbound';
    const OUTBOUND = 'outbound';
    const QUEUED = 'queued';
    const LOCAL = 'local';
    const BRIDGED = 'bridged';
    const UNKNOWN = 'unknown';


    private $calls;
    private $offset = 0;

    public function  __construct()
    {
        $this->calls = array();
        $this->calls[self::INBOUND] = array();
        $this->calls[self::OUTBOUND] = array();
        $this->calls[self::QUEUED] = array();
        $this->calls[self::LOCAL] = array();
        $this->calls[self::BRIDGED] = array();
        $this->calls[self::UNKNOWN] = array();
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function addCall($file_list, $file, $folder)
    {
        $file_link = $folder . $file;

        if (count($file_list) == 5+$this->offset)
        {
            $this->calls[self::QUEUED][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }
        else if ((strlen($file_list[2+$this->offset]) <= 4) && (strlen($file_list[1+$this->offset]) > 4))
        {
            $this->calls[self::OUTBOUND][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }
        else if ((strlen($file_list[1+$this->offset]) <= 4) && (strlen($file_list[2+$this->offset]) > 4))
        {
            $this->calls[self::INBOUND][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }
        else if ((strlen($file_list[1+$this->offset]) <= 4) && (strlen($file_list[2+$this->offset]) <= 4))
        {
            $this->calls[self::LOCAL][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }
        else if ((strlen($file_list[1+$this->offset]) > 5) && (strlen($file_list[2+$this->offset]) > 5))
        {
            $this->calls[self::BRIDGED][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }        
        else
        {
            $this->calls[self::UNKNOWN][] = array('file_list' => $file_list, 'file_link' => $file_link);
        }
    }


    private function sortComparison($a, $b)
    {
        if ($a['file_list'][0] == $b['file_list'][0])
        {
            return 0;
        }
        return ($a['file_list'][0] < $b['file_list'][0]) ? -1 : 1;
    }


    public function generateInboundTable()
    {
        echo "<h3>Inbound Calls</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>To</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                </tr>\n";


        usort($this->calls[self::INBOUND], array($this, 'sortComparison'));
        foreach ($this->calls[self::INBOUND] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);

            echo "<tr>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\" title=\"Download\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }

    public function generateOutboundTable()
    {
        echo "<h3>Outbound Calls</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>To</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                </tr>\n";

        usort($this->calls[self::OUTBOUND], array($this, 'sortComparison'));
        foreach ($this->calls[self::OUTBOUND] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);

            echo "<tr>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }

    public function generateLocalTable()
    {
        echo "<h3>Local Calls</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>To</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                </tr>\n";

        usort($this->calls[self::LOCAL], array($this, 'sortComparison'));
        foreach ($this->calls[self::LOCAL] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);

            echo "<tr>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }

    public function generateBridgedTable()
    {
        echo "<h3>Bridged Calls</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>To</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                </tr>\n";

        usort($this->calls[self::BRIDGED], array($this, 'sortComparison'));
        foreach ($this->calls[self::BRIDGED] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);

            echo "<tr>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }

    public function generateQueuedTable()
    {
        echo "<h3>Queued Calls</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>Agent/Extn</th>
                    <th>Queue Name</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                </tr>\n";

        usort($this->calls[self::QUEUED], array($this, 'sortComparison'));
        foreach ($this->calls[self::QUEUED] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);
            echo "<tr>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][4+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }

    public function generateUnknownTable()
    {
        echo "<h3>Unidentified Call Type</h3>
            <table>
                <tr class=\"top\">
                    <th>From</th>
                    <th>To</th>
                    <th>Timestamp</th>
                    <th>Duration <span style=\"font-size: 8pt;\">(min:sec)</span></th>
                    <th></th>
                    <th></th>
                </tr>\n";

        usort($this->calls[self::UNKNOWN], array($this, 'sortComparison'));

        foreach ($this->calls[self::UNKNOWN] as $call)
        {
            $datetime = $this->formatDate($call['file_list'][0]);

            echo "<tr>
            <td>{$call['file_list'][2+$this->offset]}</td>
            <td>{$call['file_list'][1+$this->offset]}</td>
            <td>{$datetime}</td>
            <td>{$call['file_list'][3+$this->offset]}</td>
            <td><a href=\"#\" class=\"player ui-state-default ui-icon ui-icon-play\"></a><a href=\"#\" class=\"ui-state-default ui-icon ui-icon-stop\"></a><a href=\"{$call['file_link']}\" download><span class=\"download ui-state-default ui-icon ui-icon-arrowthickstop-1-s\"></span></a></td>
            </tr>\n";
        }

        echo "</table>\n\n";
    }


    public function generateTableOfType($type)
    {
        if ($type == self::INBOUND)
        {
            $this->generateInboundTable();
        }
        else if ($type == self::OUTBOUND)
        {
            $this->generateOutboundTable();
        }
        else if ($type == self::LOCAL)
        {
            $this->generateLocalTable();
        }
        else if ($type == self::BRIDGED)
        {
            $this->generateBridgedTable();
        }        
        else if ($type == self::QUEUED)
        {
            $this->generateQueuedTable();
        }
        else if ($type == self::UNKNOWN)
        {
            $this->generateUnknownTable();
        }
    }


    public function hasCallsOfType($type)
    {
        return count($this->calls[$type]) > 0;
    }

    public function getCallTypes()
    {
        return array_keys($this->calls);
    }

    public function formatDate($timestamp)
    {
        return date('D, j M Y H:i:s', $timestamp);
    }

    public function resultsFound()
    {
        return $this->hasCallsOfType(self::INBOUND) ||
               $this->hasCallsOfType(self::OUTBOUND) ||
               $this->hasCallsOfType(self::LOCAL) ||
               $this->hasCallsOfType(self::BRIDGED) ||
               $this->hasCallsOfType(self::QUEUED) ||
               $this->hasCallsOfType(self::UNKNOWN);
    }
}
?>
