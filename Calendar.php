<?php

class Calendar {
    private $filename;
    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function getEvents($from = null, $to = null) {
        $items = $this->getCalendar();

        if($from) {
            $items = array_filter($items, function($item) use($from) {
                return strtotime($item['DTSTART']) >= strtotime($from) ? true : false; 
            });
        }

        if($to) {
            $items = array_filter($items, function($item) use($to) {
                return strtotime($item['DTSTART']) <= strtotime($to) ? true : false; 
            });
        }

        usort($items, function($a, $b) {
            return strtotime($a['DTSTART']) - strtotime($b['DTSTART']);
        });

        foreach($items as $item) {
            $eventDateStart = date('Y-m-d H:i:s', strtotime($item['DTSTART']));
            $eventDateEnd = isset($item['DTEND']) ? date('Y-m-d H:i:s', strtotime($item['DTEND'])) : null;
            $events[] = [
                'date' => [
                    'start' => $eventDateStart,
                    'end' => $eventDateEnd
                ],
                'title' => $item['SUMMARY'],
                'description' => $item['DESCRIPTION']
            ];
        }
        
        return $events;
    }

    private function getCalendar() {
        $content = file_get_contents($this->filename);
        $content = str_replace(';VALUE=DATE-TIME:', ':', $content);
        preg_match_all('/(BEGIN:VEVENT.*?END:VEVENT)/si', $content, $result, PREG_PATTERN_ORDER);
        
        for ($i = 0; $i < count($result[0]); $i++) {
            $lines = explode("\r\n", $result[0][$i]);

            foreach ($lines as $line) {
                $params = explode(':',$line);
                if (count($params) >1) {
                    $calendarArray[$params[0]] = $params[1];
                }
            }

            if (preg_match('/DESCRIPTION:(.*)END:VEVENT/si', $result[0][$i], $regs)) {
                $calendarArray['DESCRIPTION'] = str_replace(' ', ' ', str_replace("\r\n", '', $regs[1]));
                $calendarArray['DESCRIPTION'] = explode('LAST-MODIFIED:', $calendarArray['DESCRIPTION'])[0];
            }

            $ical[] = $calendarArray;
            unset($calendarArray);

        }
        
        return $ical;
    }
}
