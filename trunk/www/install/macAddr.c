#include <stdio.h>
#include <fcntl.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <sys/ioctl.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <linux/if.h>

long mac_addr_sys ( u_char *addr)
{
  struct ifreq ifr;
  struct ifreq *IFR;
  struct ifconf ifc;
  char buf[1024];
  int s, i;
  int ok = 0;

  s = socket(AF_INET, SOCK_DGRAM, 0);
  if (s==-1) {
    return -1;
  }

  ifc.ifc_len = sizeof(buf);
  ifc.ifc_buf = buf;
  ioctl(s, SIOCGIFCONF, &ifc);

  IFR = ifc.ifc_req;
  for (i = ifc.ifc_len / sizeof(struct ifreq); --i >= 0; IFR++) {

    strcpy(ifr.ifr_name, IFR->ifr_name);
    if (ioctl(s, SIOCGIFFLAGS, &ifr) == 0) {
      if (! (ifr.ifr_flags & IFF_LOOPBACK)) {
	if (ioctl(s, SIOCGIFHWADDR, &ifr) == 0) {
	  ok = 1;
	  break;
	}
      }
    }
  }

  close(s);
  if (ok) {
    bcopy( ifr.ifr_hwaddr.sa_data, addr, 6);
  }
  else {
    return -1;
  }
  return 0;
}

int main( int argc, char **argv)
{
  long stat;
  int i;
  u_char addr[6];

  stat = mac_addr_sys( addr);
  if (0 == stat) {
    for (i=0; i<6; ++i) {
      printf("%2.2x", addr[i]);
      if(i<5)
	printf(":");
    }
    printf( "\n");
  }
  else {
    fprintf( stderr, "can't get MAC address\n");
    exit( 1);
  }
  return 0;
}
