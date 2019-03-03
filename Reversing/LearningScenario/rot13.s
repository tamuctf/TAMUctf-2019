	.section	__TEXT,__text,regular,pure_instructions
	.build_version macos, 10, 14
	.globl	_ra                     ## -- Begin function ra
	.p2align	4, 0x90
_ra:                                    ## @ra
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	subq	$16, %rsp
	movl	$97, %eax
	movl	%edi, -8(%rbp)
	cmpl	-8(%rbp), %eax
	jg	LBB0_3
## %bb.1:
	cmpl	$122, -8(%rbp)
	jg	LBB0_3
## %bb.2:
	movl	$97, %esi
	movl	-8(%rbp), %edi
	callq	_rb
	movl	%eax, -4(%rbp)
	jmp	LBB0_7
LBB0_3:
	movl	$65, %eax
	cmpl	-8(%rbp), %eax
	jg	LBB0_6
## %bb.4:
	cmpl	$90, -8(%rbp)
	jg	LBB0_6
## %bb.5:
	movl	$65, %esi
	movl	-8(%rbp), %edi
	callq	_rb
	movl	%eax, -4(%rbp)
	jmp	LBB0_7
LBB0_6:
	movl	-8(%rbp), %eax
	movl	%eax, -4(%rbp)
LBB0_7:
	movl	-4(%rbp), %eax
	addq	$16, %rsp
	popq	%rbp
	retq
	.cfi_endproc
                                        ## -- End function
	.globl	_rb                     ## -- Begin function rb
	.p2align	4, 0x90
_rb:                                    ## @rb
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	movl	$26, %eax
	movl	%edi, -4(%rbp)
	movl	%esi, -8(%rbp)
	movl	-4(%rbp), %esi
	subl	-8(%rbp), %esi
	addl	$13, %esi
	movl	%eax, -12(%rbp)         ## 4-byte Spill
	movl	%esi, %eax
	cltd
	movl	-12(%rbp), %esi         ## 4-byte Reload
	idivl	%esi
	addl	-8(%rbp), %edx
	movl	%edx, -4(%rbp)
	movl	-4(%rbp), %edx
	movl	%edx, %eax
	popq	%rbp
	retq
	.cfi_endproc
                                        ## -- End function
	.globl	_app                    ## -- Begin function app
	.p2align	4, 0x90
_app:                                   ## @app
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	subq	$16, %rsp
	movb	%sil, %al
	movq	%rdi, -8(%rbp)
	movb	%al, -9(%rbp)
	movq	-8(%rbp), %rdi
	callq	_strlen
	movl	%eax, %esi
	movl	%esi, -16(%rbp)
	movb	-9(%rbp), %cl
	movq	-8(%rbp), %rax
	movslq	-16(%rbp), %rdi
	movb	%cl, (%rax,%rdi)
	movq	-8(%rbp), %rax
	movl	-16(%rbp), %esi
	addl	$1, %esi
	movslq	%esi, %rdi
	movb	$0, (%rax,%rdi)
	addq	$16, %rsp
	popq	%rbp
	retq
	.cfi_endproc
                                        ## -- End function
	.globl	_main                   ## -- Begin function main
	.p2align	4, 0x90
_main:                                  ## @main
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	subq	$96, %rsp
	movq	___stack_chk_guard@GOTPCREL(%rip), %rax
	movq	(%rax), %rax
	movq	%rax, -8(%rbp)
	movl	$0, -52(%rbp)
	movq	L_main.alpha(%rip), %rax
	movq	%rax, -48(%rbp)
	movq	L_main.alpha+8(%rip), %rax
	movq	%rax, -40(%rbp)
	movq	L_main.alpha+16(%rip), %rax
	movq	%rax, -32(%rbp)
	movw	L_main.alpha+24(%rip), %cx
	movw	%cx, -24(%rbp)
	movb	$0, -53(%rbp)
	movl	$0, -76(%rbp)
LBB3_1:                                 ## =>This Inner Loop Header: Depth=1
	cmpl	$3, -76(%rbp)
	jge	LBB3_3
## %bb.2:                               ##   in Loop: Header=BB3_1 Depth=1
	leaq	-61(%rbp), %rdi
	movb	$-106, -69(%rbp)
	movb	$25, -56(%rbp)
	movb	$13, -54(%rbp)
	movb	$2, -55(%rbp)
	movzbl	-69(%rbp), %eax
	movsbl	-56(%rbp), %ecx
	addl	%ecx, %eax
	movsbl	-54(%rbp), %ecx
	cltd
	idivl	%ecx
	movsbl	-55(%rbp), %ecx
	movl	%ecx, %edx
	imull	%edx, %edx
	imull	%ecx, %edx
	imull	%edx, %eax
	movsbl	-53(%rbp), %ecx
	movl	%ecx, %edx
	imull	%edx, %edx
	imull	%ecx, %edx
	addl	%edx, %eax
	movb	%al, %sil
	movb	%sil, -67(%rbp)
	movzbl	-67(%rbp), %eax
	cvtsi2sdl	%eax, %xmm0
	movq	%rdi, -88(%rbp)         ## 8-byte Spill
	callq	_round
	cvttsd2si	%xmm0, %eax
	movb	%al, %sil
	movb	%sil, -68(%rbp)
	movsbl	-53(%rbp), %eax
	addl	$1, %eax
	movb	%al, %sil
	movb	%sil, -53(%rbp)
	movq	-88(%rbp), %rdi         ## 8-byte Reload
	movsbl	-68(%rbp), %esi
	callq	_app
	movl	-76(%rbp), %eax
	addl	$1, %eax
	movl	%eax, -76(%rbp)
	jmp	LBB3_1
LBB3_3:
	movl	$5, -80(%rbp)
	movl	$0, -76(%rbp)
LBB3_4:                                 ## =>This Inner Loop Header: Depth=1
	movl	-76(%rbp), %eax
	cmpl	-80(%rbp), %eax
	jg	LBB3_7
## %bb.5:                               ##   in Loop: Header=BB3_4 Depth=1
	movslq	-76(%rbp), %rax
	movsbl	-61(%rbp,%rax), %edi
	callq	_ra
	movb	%al, %cl
	movslq	-76(%rbp), %rdx
	movb	%cl, -66(%rbp,%rdx)
## %bb.6:                               ##   in Loop: Header=BB3_4 Depth=1
	movl	-76(%rbp), %eax
	addl	$1, %eax
	movl	%eax, -76(%rbp)
	jmp	LBB3_4
LBB3_7:
	movq	___stack_chk_guard@GOTPCREL(%rip), %rax
	movq	(%rax), %rax
	movq	-8(%rbp), %rcx
	cmpq	%rcx, %rax
	jne	LBB3_9
## %bb.8:
	xorl	%eax, %eax
	addq	$96, %rsp
	popq	%rbp
	retq
LBB3_9:
	callq	___stack_chk_fail
	ud2
	.cfi_endproc
                                        ## -- End function
	.section	__TEXT,__cstring,cstring_literals
	.p2align	4               ## @main.alpha
L_main.alpha:
	.asciz	"abcdefghijklmnopqrstuvxyz"


.subsections_via_symbols
